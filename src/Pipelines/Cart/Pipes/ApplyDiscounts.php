<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;

class ApplyDiscounts
{
    /**
     * Called just before cart totals are calculated.
     */
    public function handle(Model $cart, Closure $next): Model
    {
        return $next($cart);
    }
}
