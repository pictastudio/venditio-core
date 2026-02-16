<?php

namespace PictaStudio\Venditio\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\Venditio\Actions\Taxes\ExtractTaxFromGrossPrice;

use function PictaStudio\Venditio\Helpers\Functions\query;

class CalculateTaxes
{
    public function __construct(
        private readonly ExtractTaxFromGrossPrice $extractTaxFromGrossPrice,
    ) {}

    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $unitFinalPrice = (float) $cartLine->unit_final_price;

        $taxRate = $this->getTaxRate(
            $cartLine->getAttribute('product_data'),
            $this->getCartCountryId($cartLine)
        );
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

    private function getTaxRate(array $product, ?int $countryId): float
    {
        $taxClassId = Arr::get($product, 'tax_class_id');

        $query = query('country_tax_class')
            ->where('tax_class_id', $taxClassId);

        if (filled($countryId)) {
            $countryRate = (clone $query)
                ->where('country_id', $countryId)
                ->value('rate');

            if ($countryRate !== null) {
                return (float) $countryRate;
            }
        }

        return (float) $query->value('rate');
    }

    private function isPriceTaxInclusive(array $product): bool
    {
        return (bool) Arr::get($product, 'inventory.price_includes_tax', false);
    }

    private function getCartCountryId(Model $cartLine): ?int
    {
        $cart = $cartLine->relationLoaded('cart') && $cartLine->cart instanceof Model
            ? $cartLine->cart
            : query('cart')->find($cartLine->getAttribute('cart_id'));

        if (!$cart instanceof Model) {
            return null;
        }

        $addresses = $cart->getAttribute('addresses');
        $shippingCountryId = Arr::get($addresses, 'shipping.country_id');
        $billingCountryId = Arr::get($addresses, 'billing.country_id');
        $countryId = $shippingCountryId ?? $billingCountryId;

        return is_numeric($countryId) ? (int) $countryId : null;
    }
}
