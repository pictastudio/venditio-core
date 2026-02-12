<?php

namespace PictaStudio\Venditio\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CartIdentifierGeneratorInterface
{
    /**
     * Generate an identifier for the cart.
     */
    public function generate(Model $cart): string;
}
