<?php

namespace PictaStudio\VenditioCore\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\DiscountRuleInterface;
use PictaStudio\VenditioCore\Discounts\DiscountContext;
use PictaStudio\VenditioCore\Models\Discount;

class OncePerCartRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        if (!$discount->apply_once_per_cart) {
            return true;
        }

        return !$context->hasDiscountBeenAppliedInCart($discount);
    }
}
