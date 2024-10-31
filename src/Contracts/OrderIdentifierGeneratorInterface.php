<?php

namespace PictaStudio\VenditioCore\Contracts;

use Illuminate\Database\Eloquent\Model;

interface OrderIdentifierGeneratorInterface
{
    /**
     * Generate an identifier for the order.
     */
    public function generate(Model $order): string;
}
