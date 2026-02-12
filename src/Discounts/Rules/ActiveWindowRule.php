<?php

namespace PictaStudio\Venditio\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Contracts\DiscountRuleInterface;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

class ActiveWindowRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        if (!$discount->active) {
            return false;
        }

        if ($discount->starts_at?->isFuture()) {
            return false;
        }

        return !($discount->ends_at?->isPast());
    }
}
