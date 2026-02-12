<?php

namespace PictaStudio\VenditioCore\Contracts;

use Illuminate\Database\Eloquent\Model;

interface DiscountUsageRecorderInterface
{
    public function recordFromOrder(Model $order): void;
}
