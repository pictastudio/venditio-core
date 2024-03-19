<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CalculateTaxes
{
    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $price = $cartLine->unit_discount;
        $unitDiscount = $cartLine->unit_final_price;
        $unitFinalPrice = $cartLine->unit_final_price;

        $taxRate = 0; // get 'rate' from 'country_tax_class' after getting the taxClass from the product
        $unitFinalPriceTax = 0; // tassa unitaria calcolata su unit_final_price
        $unitFinalPriceTaxable = $unitFinalPrice - $unitFinalPriceTax;

        $cartLine->fill([
            'unit_final_price_tax' => $unitFinalPriceTax,
            'unit_final_price_taxable' => $unitFinalPriceTaxable,
            'tax_rate' => $taxRate,
        ]);

        return $next($cartLine);
    }
}
