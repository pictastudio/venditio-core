<?php

namespace PictaStudio\Venditio\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CalculateShippingFees
{
    public function __invoke(Model $cart, Closure $next): Model
    {
        return $next($cart);
    }
}
