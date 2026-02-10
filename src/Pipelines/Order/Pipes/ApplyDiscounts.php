<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Contracts\{CartTotalDiscountCalculatorInterface, DiscountCalculatorInterface};
use PictaStudio\VenditioCore\Discounts\DiscountContext;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ApplyDiscounts
{
    public function __construct(
        private readonly DiscountCalculatorInterface $discountCalculator,
        private readonly CartTotalDiscountCalculatorInterface $cartTotalDiscountCalculator,
    ) {}

    public function __invoke(Model $order, Closure $next): Model
    {
        $lines = $order->getRelation('lines');

        if (!$lines instanceof Collection) {
            $lines = collect($lines ?? []);
        }

        $sourceCart = $order->getRelation('sourceCart');
        $user = filled($order->getAttribute('user_id'))
            ? query('user')->find($order->getAttribute('user_id'))
            : null;

        $context = DiscountContext::make(
            cart: $sourceCart,
            order: $order,
            user: $user,
        );

        $lines = $lines->map(function (Model $line) use ($context, $order) {
            $line->setRelation('order', $order);
            $this->discountCalculator->apply($line, $context);
            $this->recalculateLineTotals($line);

            return $line;
        });

        $cartTotalDiscount = $this->cartTotalDiscountCalculator->resolveForTarget($order, $lines, $context);

        $order->setRelation('lines', $lines);
        $order->fill([
            'discount_amount' => $cartTotalDiscount['discount_amount'],
            'discount_code' => $cartTotalDiscount['discount_code'],
        ]);

        return $next($order);
    }

    private function recalculateLineTotals(Model $line): void
    {
        $unitFinalPrice = (float) $line->getAttribute('unit_final_price');
        $taxRate = (float) ($line->getAttribute('tax_rate') ?? 0);
        $qty = max(1, (int) ($line->getAttribute('qty') ?? 1));
        $unitFinalPriceTax = round($unitFinalPrice * ($taxRate / 100), 2);

        $line->fill([
            'unit_final_price_taxable' => $unitFinalPrice,
            'unit_final_price_tax' => $unitFinalPriceTax,
            'total_final_price' => round(($unitFinalPrice + $unitFinalPriceTax) * $qty, 2),
        ]);
    }
}
