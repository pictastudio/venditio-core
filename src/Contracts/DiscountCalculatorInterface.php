<?php

namespace PictaStudio\VenditioCore\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Discounts\DiscountContext;

interface DiscountCalculatorInterface
{
    public function apply(Model $line, DiscountContext $context): Model;
}
