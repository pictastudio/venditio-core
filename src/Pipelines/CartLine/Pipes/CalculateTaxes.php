<?php

namespace PictaStudio\Venditio\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\Venditio\Actions\Taxes\ExtractTaxFromGrossPrice;
use PictaStudio\Venditio\Models\CountryTaxClass;

class CalculateTaxes
{
    public function __construct(
        private readonly ExtractTaxFromGrossPrice $extractTaxFromGrossPrice,
    ) {}

    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $unitFinalPrice = (float) $cartLine->unit_final_price;

        $taxRate = $this->getTaxRate($cartLine->getAttribute('product_data'));
        $priceIncludesTax = $this->isPriceTaxInclusive($cartLine->getAttribute('product_data'));

        if ($priceIncludesTax) {
            $taxBreakdown = $this->extractTaxFromGrossPrice->handle($unitFinalPrice, $taxRate);
            $unitFinalPriceTaxable = $taxBreakdown['taxable'];
            $unitFinalPriceTax = $taxBreakdown['tax'];
        } else {
            $unitFinalPriceTaxable = $unitFinalPrice;
            $unitFinalPriceTax = round($unitFinalPrice * ($taxRate / 100), 2);
        }

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

    private function isPriceTaxInclusive(array $product): bool
    {
        return (bool) Arr::get($product, 'inventory.price_includes_tax', false);
    }
}
