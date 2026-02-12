<?php

namespace PictaStudio\VenditioCore\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\DiscountRuleInterface;
use PictaStudio\VenditioCore\Discounts\DiscountContext;
use PictaStudio\VenditioCore\Models\Discount;

class LineScopeRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        return !$discount->apply_to_cart_total;
    }
}
