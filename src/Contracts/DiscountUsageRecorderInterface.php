<?php

namespace PictaStudio\Venditio\Contracts;

use Illuminate\Database\Eloquent\Model;

interface DiscountUsageRecorderInterface
{
    public function recordFromOrder(Model $order): void;
}
