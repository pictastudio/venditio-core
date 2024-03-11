<?php

namespace PictaStudio\VenditioCore\Helpers\Cart\Contracts;

use PictaStudio\VenditioCore\Models\Cart;

interface CartIdentifierGeneratorInterface
{
    /**
     * Generate an identifier for the cart.
     */
    public function generate(Cart $cart): string;
}
