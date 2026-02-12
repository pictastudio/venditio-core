<?php

namespace PictaStudio\VenditioCore\Discounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Contracts\DiscountUsageRecorderInterface;
use PictaStudio\VenditioCore\Models\Discount;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class DiscountUsageRecorder implements DiscountUsageRecorderInterface
{
    public function recordFromOrder(Model $order): void
    {
        $lines = $order->relationLoaded('lines')
            ? $order->getRelation('lines')
            : $order->lines;

        $discountCodes = $lines
            ->pluck('discount_code')
            ->filter(fn (mixed $code) => filled($code))
            ->unique()
            ->values();

        if ($discountCodes->isEmpty()) {
            return;
        }

        $discounts = $this->loadDiscountsByCode($discountCodes);
        $increments = [];
        $usageModel = resolve_model('discount_application');

        $lines->each(function (Model $line) use ($order, $discounts, $usageModel, &$increments) {
            $discountCode = $line->getAttribute('discount_code');

            if (blank($discountCode)) {
                return;
            }

            /** @var Discount|null $discount */
            $discount = $discounts->get($discountCode);

            if (!$discount instanceof Discount) {
                return;
            }

            $usage = $usageModel::query()->firstOrCreate(
                ['order_line_id' => $line->getKey()],
                [
                    'discount_id' => $discount->getKey(),
                    'discountable_type' => resolve_model('product'),
                    'discountable_id' => $line->getAttribute('product_id'),
                    'user_id' => $order->getAttribute('user_id'),
                    'cart_id' => $order->getRelation('sourceCart')?->getKey(),
                    'order_id' => $order->getKey(),
                    'qty' => (int) ($line->getAttribute('qty') ?? 1),
                    'amount' => (float) ($line->getAttribute('discount_amount') ?? 0),
                ]
            );

            if ($usage->wasRecentlyCreated) {
                $discountId = $discount->getKey();
                $increments[$discountId] = ($increments[$discountId] ?? 0) + 1;
            }
        });

        $this->recordOrderLevelDiscountUsage($order, $discounts, $increments);
        $this->incrementDiscountUses($increments);
    }

    /**
     * @param  Collection<int, string>  $discountCodes
     * @return Collection<string, Discount>
     */
    private function loadDiscountsByCode(Collection $discountCodes): Collection
    {
        $discountModel = resolve_model('discount');

        return $discountModel::query()
            ->withoutGlobalScopes()
            ->whereIn('code', $discountCodes->all())
            ->get()
            ->keyBy('code');
    }

    private function incrementDiscountUses(array $increments): void
    {
        if (blank($increments)) {
            return;
        }

        $discountModel = resolve_model('discount');

        foreach ($increments as $discountId => $uses) {
            $discountModel::query()
                ->withoutGlobalScopes()
                ->whereKey($discountId)
                ->increment('uses', $uses);
        }
    }

    /**
     * @param  Collection<string, Discount>  $discounts
     * @param  array<int|string, int>  $increments
     */
    private function recordOrderLevelDiscountUsage(Model $order, Collection $discounts, array &$increments): void
    {
        $orderDiscountCode = $order->getAttribute('discount_code');
        $orderDiscountAmount = (float) ($order->getAttribute('discount_amount') ?? 0);

        if (blank($orderDiscountCode) || $orderDiscountAmount <= 0) {
            return;
        }

        /** @var Discount|null $discount */
        $discount = $discounts->get($orderDiscountCode)
            ?? $this->loadDiscountsByCode(collect([$orderDiscountCode]))->get($orderDiscountCode);

        if (!$discount instanceof Discount) {
            return;
        }

        $usageModel = resolve_model('discount_application');

        $usage = $usageModel::query()->firstOrCreate(
            [
                'discount_id' => $discount->getKey(),
                'order_id' => $order->getKey(),
                'order_line_id' => null,
            ],
            [
                'discountable_type' => $order->getMorphClass(),
                'discountable_id' => $order->getKey(),
                'user_id' => $order->getAttribute('user_id'),
                'cart_id' => $order->getRelation('sourceCart')?->getKey(),
                'qty' => 1,
                'amount' => $orderDiscountAmount,
            ]
        );

        if ($usage->wasRecentlyCreated) {
            $discountId = $discount->getKey();
            $increments[$discountId] = ($increments[$discountId] ?? 0) + 1;
        }
    }
}
