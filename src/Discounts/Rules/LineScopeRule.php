<?php

namespace PictaStudio\Venditio\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Contracts\DiscountRuleInterface;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

class LineScopeRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        return !$discount->apply_to_cart_total;
    }
}
