<?php

namespace PictaStudio\Venditio\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\Venditio\Contracts\DiscountRuleInterface;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

class MinimumOrderTotalRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        $minimumOrderTotal = (float) ($discount->minimum_order_total ?? 0);

        if ($minimumOrderTotal <= 0) {
            return true;
        }

        return $this->resolveCurrentTotal($line, $context) >= $minimumOrderTotal;
    }

    private function resolveCurrentTotal(Model $line, DiscountContext $context): float
    {
        $targets = [
            $context->getCart(),
            $context->getOrder(),
            $line->relationLoaded('cart') ? $line->getRelation('cart') : null,
            $line->relationLoaded('order') ? $line->getRelation('order') : null,
        ];

        foreach ($targets as $target) {
            if (!$target instanceof Model) {
                continue;
            }

            $linesTotal = $this->sumLinesTotal($target);

            if ($linesTotal > 0) {
                return $linesTotal;
            }

            $subTotal = (float) ($target->getAttribute('sub_total') ?? 0);

            if ($subTotal > 0) {
                return $subTotal;
            }
        }

        return (float) ($line->getAttribute('total_final_price') ?? 0);
    }

    private function sumLinesTotal(Model $target): float
    {
        if (!$target->relationLoaded('lines')) {
            return 0;
        }

        $lines = $target->getRelation('lines');

        if (!$lines instanceof Collection || $lines->isEmpty()) {
            return 0;
        }

        return (float) $lines->sum('total_final_price');
    }
}
