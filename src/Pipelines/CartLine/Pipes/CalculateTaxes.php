<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Models\CountryTaxClass;

class CalculateTaxes
{
    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $unitFinalPrice = $cartLine->unit_final_price;

        $taxRate = $this->getTaxRate($cartLine->getAttribute('product'));

        // il valore imponibile è uguale al prezzo finale (trattiamo il prezzo flat dei prodotti, privo di tasse)
        $unitFinalPriceTaxable = $unitFinalPrice;

        $unitFinalPriceTax = $unitFinalPrice * $taxRate / 100;

        $cartLine->fill([
            'unit_final_price_tax' => $unitFinalPriceTax,
            'unit_final_price_taxable' => $unitFinalPriceTaxable,
            'tax_rate' => $taxRate,
        ]);

        return $next($cartLine);
    }

    private function getTaxRate(array $product): float
    {
        $taxClassId = Arr::get($product, 'tax_class_id');

        return CountryTaxClass::query()
            ->where('tax_class_id', $taxClassId)
            ->firstOrFail()
            ->rate;
    }
}
