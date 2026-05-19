<?php

namespace PictaStudio\Venditio\Discounts;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Collection;
use PictaStudio\Venditio\Contracts\{DiscountCalculatorInterface, DiscountRuleInterface, DiscountablesResolverInterface};
use PictaStudio\Venditio\Enums\DiscountType;
use PictaStudio\Venditio\Models\Discount;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class DiscountCalculator implements DiscountCalculatorInterface
{
    public function __construct(
        private readonly DiscountablesResolverInterface $discountablesResolver,
    ) {}

    public function apply(Model $line, DiscountContext $context): Model
    {
        $unitPrice = (float) $line->getAttribute('unit_price');
        $qty = max(1, (int) ($line->getAttribute('qty') ?? 1));

        if (!$this->lineAllowsDiscounts($line)) {
            $line->fill([
                'discount_id' => null,
                'discount_code' => null,
                'discount_amount' => 0,
                'unit_discount' => 0,
                'unit_final_price' => round(max(0, $unitPrice), 2),
            ]);
            $this->syncCalculatedPriceSnapshot($line, round(max(0, $unitPrice), 2), collect(), $qty, $unitPrice);

            return $line;
        }

        $appliedDiscounts = $this->resolveApplicableDiscounts($line, $context, $unitPrice);
        $primaryDiscount = $appliedDiscounts->first()['discount'] ?? null;
        $totalUnitDiscount = round((float) $appliedDiscounts->sum('amount'), 2);
        $unitFinalPrice = round(max(0, $unitPrice - $totalUnitDiscount), 2);

        $line->fill([
            'discount_id' => $primaryDiscount?->getKey(),
            'discount_code' => $primaryDiscount?->code,
            'discount_amount' => round($totalUnitDiscount * $qty, 2),
            'unit_discount' => $totalUnitDiscount,
            'unit_final_price' => $unitFinalPrice,
        ]);
        $this->syncCalculatedPriceSnapshot($line, $unitFinalPrice, $appliedDiscounts, $qty, $unitPrice);

        return $line;
    }

    private function syncCalculatedPriceSnapshot(
        Model $line,
        float $unitFinalPrice,
        Collection $appliedDiscounts,
        int $qty,
        float $unitPrice,
    ): void {
        $productData = $line->getAttribute('product_data');

        if (!is_array($productData)) {
            return;
        }

        if (blank(data_get($productData, 'price_calculated.price'))) {
            data_set($productData, 'price_calculated.price', (float) $line->getAttribute('unit_price'));
        }

        data_set($productData, 'price_calculated.discounts_applied', $this->toDiscountSnapshot($appliedDiscounts, $qty, $unitPrice));
        data_set($productData, 'price_calculated.price_final', $unitFinalPrice);
        $line->setAttribute('product_data', $productData);
    }

    private function lineAllowsDiscounts(Model $line): bool
    {
        $productData = $line->getAttribute('product_data');

        if (!is_array($productData)) {
            return true;
        }

        $allowDiscounts = data_get(
            $productData,
            'price_calculated.price_source.price_list.allow_discounts',
            data_get(
                $productData,
                'pricing.price_source.price_list.allow_discounts',
                data_get(
                    $productData,
                    'price_calculated.price_list.allow_discounts',
                    data_get(
                        $productData,
                        'pricing.price_list.allow_discounts',
                        data_get(
                            $productData,
                            'price_calculated.price_source.allow_discounts',
                            data_get($productData, 'pricing.price_source.allow_discounts', true)
                        )
                    )
                )
            )
        );

        return filter_var($allowDiscounts, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
    }

    private function toDiscountSnapshot(Collection $appliedDiscounts, int $qty, float $startingUnitPrice): array
    {
        $currentUnitPrice = round(max(0, $startingUnitPrice), 2);

        return $appliedDiscounts
            ->values()
            ->map(function (array $evaluation, int $index) use ($qty, &$currentUnitPrice): array {
                /** @var Discount $discount */
                $discount = $evaluation['discount'];
                $unitAmount = round((float) ($evaluation['amount'] ?? 0), 2);
                $unitPriceBefore = $currentUnitPrice;
                $unitPriceAfter = round(max(0, $unitPriceBefore - $unitAmount), 2);
                $currentUnitPrice = $unitPriceAfter;

                return [
                    'position' => $index + 1,
                    'id' => $discount->getKey(),
                    'name' => $discount->name,
                    'code' => $discount->code,
                    'discountable_type' => $discount->discountable_type,
                    'discountable_id' => $discount->discountable_id,
                    'type' => is_object($discount->type) && isset($discount->type->value)
                        ? $discount->type->value
                        : $discount->type,
                    'value' => round((float) ($discount->value ?? 0), 2),
                    'priority' => (int) ($discount->priority ?? 0),
                    'standalone' => (bool) ($discount->standalone ?? false),
                    'amount' => round($unitAmount * $qty, 2),
                    'unit_amount' => $unitAmount,
                    'unit_price_before' => $unitPriceBefore,
                    'unit_price_after' => $unitPriceAfter,
                ];
            })
            ->values()
            ->all();
    }

    private function resolveApplicableDiscounts(Model $line, DiscountContext $context, float $unitPrice): Collection
    {
        $discounts = $this->queryDiscountsForLine($line, $context)
            ->filter(fn (Discount $discount) => $this->passesRules($discount, $line, $context))
            ->values();

        if ($discounts->isEmpty()) {
            return collect();
        }

        $discounts = $this->sortDiscounts($discounts, $unitPrice);
        $cumulativeDiscounts = $this->evaluateCumulativeDiscounts(
            $discounts->reject(fn (Discount $discount): bool => (bool) ($discount->standalone ?? false)),
            $unitPrice,
        );
        $standaloneDiscounts = $this->evaluateStandaloneDiscounts(
            $discounts->filter(fn (Discount $discount): bool => (bool) ($discount->standalone ?? false)),
            $unitPrice,
        );
        $winningDiscounts = $this->chooseWinningDiscounts($cumulativeDiscounts, $standaloneDiscounts);

        $winningDiscounts->each(function (array $evaluation) use ($context): void {
            $discount = $evaluation['discount'];
            $context->markDiscountAsAppliedInCart($discount);
        });

        return $winningDiscounts;
    }

    private function sortDiscounts(Collection $discounts, float $unitPrice): Collection
    {
        return $discounts
            ->map(fn (Discount $discount): array => [
                'discount' => $discount,
                'amount' => $this->calculateUnitDiscount($discount, $unitPrice),
            ])
            ->sort(fn (array $a, array $b): int => $this->sortByPriorityAndAmount($a, $b))
            ->map(fn (array $evaluation): Discount => $evaluation['discount'])
            ->values();
    }

    private function evaluateCumulativeDiscounts(Collection $discounts, float $unitPrice): Collection
    {
        $currentUnitPrice = round(max(0, $unitPrice), 2);
        $evaluations = collect();

        foreach ($discounts as $discount) {
            if (!$discount instanceof Discount || $currentUnitPrice <= 0) {
                continue;
            }

            $amount = $this->calculateUnitDiscount($discount, $currentUnitPrice);

            if ($amount <= 0) {
                continue;
            }

            $evaluations->push([
                'discount' => $discount,
                'amount' => $amount,
            ]);

            $currentUnitPrice = round(max(0, $currentUnitPrice - $amount), 2);
        }

        return $evaluations->values();
    }

    private function evaluateStandaloneDiscounts(Collection $discounts, float $unitPrice): Collection
    {
        return $discounts
            ->map(fn (Discount $discount): array => [
                'discount' => $discount,
                'amount' => $this->calculateUnitDiscount($discount, $unitPrice),
            ])
            ->filter(fn (array $evaluation): bool => $evaluation['amount'] > 0)
            ->sort(fn (array $a, array $b): int => $this->sortByPriorityAndAmount($a, $b))
            ->values();
    }

    private function chooseWinningDiscounts(Collection $cumulativeDiscounts, Collection $standaloneDiscounts): Collection
    {
        $cumulativeAmount = round((float) $cumulativeDiscounts->sum('amount'), 2);
        $standaloneWinner = $standaloneDiscounts->first();

        if (!is_array($standaloneWinner)) {
            return $cumulativeDiscounts;
        }

        if (round((float) $standaloneWinner['amount'], 2) > $cumulativeAmount) {
            return collect([$standaloneWinner]);
        }

        return $cumulativeDiscounts;
    }

    private function queryDiscountsForLine(Model $line, DiscountContext $context): Collection
    {
        $discountModel = resolve_model('discount');
        $discountables = $this->discountablesResolver->resolve($line, $context);

        if ($discountables->isEmpty()) {
            return collect();
        }

        /** @var Builder $query */
        $query = $discountModel::query();

        $query->where(function (Builder $builder) use ($discountables) {
            $discountables->each(function (Model $discountable) use ($builder) {
                $builder->orWhere(function (Builder $query) use ($discountable) {
                    $query->where('discountable_type', $discountable->getMorphClass())
                        ->where('discountable_id', $discountable->getKey());
                });
            });
        });

        return $query->orderByDesc('priority')->get();
    }

    private function passesRules(Discount $discount, Model $line, DiscountContext $context): bool
    {
        $ruleClasses = config('venditio.discounts.rules', []);

        foreach ($ruleClasses as $ruleClass) {
            /** @var DiscountRuleInterface $rule */
            $rule = app($ruleClass);

            if (!$rule->passes($discount, $line, $context)) {
                return false;
            }
        }

        return true;
    }

    private function calculateUnitDiscount(Discount $discount, float $unitPrice): float
    {
        $rawDiscount = match ($discount->type) {
            DiscountType::Percentage => $unitPrice * ((float) $discount->value / 100),
            DiscountType::Fixed => (float) $discount->value,
            DiscountType::FixedPrice => $unitPrice - (float) $discount->value,
            default => 0,
        };

        return round(min($unitPrice, max(0, $rawDiscount)), 2);
    }

    private function sortByPriorityAndAmount(array $a, array $b): int
    {
        /** @var Discount $left */
        $left = $a['discount'];
        /** @var Discount $right */
        $right = $b['discount'];

        $leftPriority = (int) $left->priority;
        $rightPriority = (int) $right->priority;

        if ($this->isProductScoped($left) !== $this->isProductScoped($right)) {
            return $this->isProductScoped($right) <=> $this->isProductScoped($left);
        }

        if ($leftPriority !== $rightPriority) {
            return $rightPriority <=> $leftPriority;
        }

        $amountComparison = $b['amount'] <=> $a['amount'];

        if ($amountComparison !== 0) {
            return $amountComparison;
        }

        return (int) $left->getKey() <=> (int) $right->getKey();
    }

    private function isProductScoped(Discount $discount): bool
    {
        $productModel = resolve_model('product');
        $productMorphClass = (new $productModel)->getMorphClass();

        return $discount->discountable_type === $productMorphClass;
    }
}
