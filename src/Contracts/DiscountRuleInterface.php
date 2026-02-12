<?php

namespace PictaStudio\VenditioCore\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Discounts\DiscountContext;
use PictaStudio\VenditioCore\Models\Discount;

interface DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool;
}
