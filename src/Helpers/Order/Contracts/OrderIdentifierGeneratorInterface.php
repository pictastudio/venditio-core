<?php

namespace PictaStudio\VenditioCore\Helpers\Order\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Models\Order;

interface OrderIdentifierGeneratorInterface
{
    /**
     * Generate an identifier for the order.
     */
    public function generate(Model $order): string;
}
