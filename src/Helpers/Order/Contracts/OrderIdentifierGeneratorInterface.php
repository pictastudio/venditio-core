<?php

namespace PictaStudio\VenditioCore\Helpers\Order\Contracts;

use PictaStudio\VenditioCore\Models\Order;

interface OrderIdentifierGeneratorInterface
{
    /**
     * Generate an identifier for the order.
     */
    public function generate(Order $order): string;
}
