<?php

namespace PictaStudio\VenditioCore\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\DiscountRuleInterface;
use PictaStudio\VenditioCore\Discounts\DiscountContext;
use PictaStudio\VenditioCore\Models\Discount;

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
