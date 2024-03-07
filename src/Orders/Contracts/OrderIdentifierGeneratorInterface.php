<?php

namespace PictaStudio\VenditioCore\Orders\Contracts;

use PictaStudio\VenditioCore\Models\Order;

interface OrderIdentifierGeneratorInterface
{
    /**
     * Generate an identifier for the order.
     */
    public function generate(Order $order): string;
}
