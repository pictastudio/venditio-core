<?php

namespace PictaStudio\Venditio\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Contracts\DiscountRuleInterface;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

class MaxUsesRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        if (blank($discount->max_uses)) {
            return true;
        }

        return $discount->uses < $discount->max_uses;
    }
}
