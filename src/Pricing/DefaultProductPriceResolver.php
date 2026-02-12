<?php

namespace PictaStudio\VenditioCore\Pricing;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\ProductPriceResolverInterface;

class DefaultProductPriceResolver implements ProductPriceResolverInterface
{
    public function resolve(Model $product): array
    {
        if (!config('venditio-core.price_lists.enabled', false)) {
            return $this->fromInventory($product);
        }

        $priceListPrice = $this->resolvePriceListPrice($product);

        if (!$priceListPrice instanceof Model) {
            return $this->fromInventory($product);
        }

        $priceList = $priceListPrice->relationLoaded('priceList')
            ? $priceListPrice->getRelation('priceList')
            : $priceListPrice->priceList()->first();

        return [
            'unit_price' => (float) ($priceListPrice->getAttribute('price') ?? 0),
            'purchase_price' => $priceListPrice->getAttribute('purchase_price') === null
                ? null
                : (float) $priceListPrice->getAttribute('purchase_price'),
            'price_includes_tax' => (bool) $priceListPrice->getAttribute('price_includes_tax'),
            'price_list' => $priceList instanceof Model
                ? [
                    'id' => $priceList->getKey(),
                    'name' => (string) $priceList->getAttribute('name'),
                ]
                : null,
        ];
    }

    protected function resolvePriceListPrice(Model $product): ?Model
    {
        if (!method_exists($product, 'priceListPrices')) {
            return null;
        }

        $priceListPrices = $product->relationLoaded('priceListPrices')
            ? $product->getRelation('priceListPrices')
            : $product->priceListPrices()->with('priceList')->get();

        return $priceListPrices
            ->firstWhere('is_default', true)
            ?? $priceListPrices->first();
    }

    protected function fromInventory(Model $product): array
    {
        if (!method_exists($product, 'inventory')) {
            return [
                'unit_price' => 0,
                'purchase_price' => null,
                'price_includes_tax' => false,
                'price_list' => null,
            ];
        }

        $inventory = $product->relationLoaded('inventory')
            ? $product->getRelation('inventory')
            : $product->inventory()->first();

        return [
            'unit_price' => (float) ($inventory?->getAttribute('price') ?? 0),
            'purchase_price' => $inventory?->getAttribute('purchase_price') === null
                ? null
                : (float) $inventory?->getAttribute('purchase_price'),
            'price_includes_tax' => (bool) ($inventory?->getAttribute('price_includes_tax') ?? false),
            'price_list' => null,
        ];
    }
}
