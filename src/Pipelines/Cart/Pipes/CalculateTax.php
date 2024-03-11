<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use PictaStudio\VenditioCore\Models\Cart;

class CalculateTax
{
    /**
     * Called just before cart totals are calculated.
     */
    public function handle(Cart $cart, Closure $next): Closure
    {
        return $next($cart);
    }
}
