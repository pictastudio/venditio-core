<?php

namespace PictaStudio\VenditioCore\Helpers\Cart\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Models\Cart;

interface CartIdentifierGeneratorInterface
{
    /**
     * Generate an identifier for the cart.
     */
    public function generate(Model $cart): string;
}
