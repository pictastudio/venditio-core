<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;

class ApplyDiscount
{
    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $discount = 0;
        $unitDiscount = 0;

        if ($discount) {
            $unitDiscount = $cartLine->unit_price * ($discount / 100);
        }

        $cartLine->fill([
            'unit_discount' => $unitDiscount,
            'unit_final_price' => $cartLine->unit_price - $unitDiscount,
        ]);

        return $next($cartLine);
    }
}
