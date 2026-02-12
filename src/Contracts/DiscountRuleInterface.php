<?php

namespace PictaStudio\Venditio\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

interface DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool;
}
