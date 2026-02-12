<?php

namespace PictaStudio\Venditio\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Discounts\DiscountContext;

interface DiscountCalculatorInterface
{
    public function apply(Model $line, DiscountContext $context): Model;
}
