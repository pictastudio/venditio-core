<?php

namespace PictaStudio\Venditio\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CalculateTotal
{
    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $unitFinalPriceTax = $cartLine->unit_final_price_tax;
        $unitFinalPriceTaxable = $cartLine->unit_final_price_taxable;

        $cartLine->fill([
            'total_final_price' => ($unitFinalPriceTaxable + $unitFinalPriceTax) * $cartLine->qty,
        ]);

        return $next($cartLine);
    }
}
