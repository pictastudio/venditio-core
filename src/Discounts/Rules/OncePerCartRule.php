<?php

namespace PictaStudio\Venditio\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Contracts\DiscountRuleInterface;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

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
