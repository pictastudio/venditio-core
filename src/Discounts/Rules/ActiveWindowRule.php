<?php

namespace PictaStudio\VenditioCore\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\DiscountRuleInterface;
use PictaStudio\VenditioCore\Discounts\DiscountContext;
use PictaStudio\VenditioCore\Models\Discount;

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
