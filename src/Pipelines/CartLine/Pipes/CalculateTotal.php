<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CalculateTotal
{
    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $cartLine->fill([
            'total_final_price' => $cartLine->unit_final_price_taxable * $cartLine->qty,
        ]);

        return $next($cartLine);
    }
}
