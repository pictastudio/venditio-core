<?php

namespace PictaStudio\Venditio\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ProductSnapshot
{
    private const PRODUCT_KEYS = [
        'id',
        'parent_id',
        'brand_id',
        'product_type_id',
        'tax_class_id',
        'name',
        'slug',
        'sku',
        'ean',
        'images',
        'files',
        'measuring_unit',
        'qty_for_unit',
        'length',
        'width',
        'height',
        'weight',
    ];

    public static function make(Model $product): array
    {
        return self::fromArray($product->attributesToArray());
    }

    public static function fromArray(mixed $productData): array
    {
        if (!is_array($productData)) {
            return [];
        }

        $snapshot = collect($productData)
            ->only(self::PRODUCT_KEYS)
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();

        $inventory = collect([
            'currency_id' => Arr::get($productData, 'inventory.currency_id'),
            'price_includes_tax' => Arr::has($productData, 'inventory.price_includes_tax')
                ? (bool) Arr::get($productData, 'inventory.price_includes_tax')
                : null,
        ])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();

        if ($inventory !== []) {
            $snapshot['inventory'] = $inventory;
        }

        $pricing = collect([
            'price_list' => self::priceListSummary(Arr::get($productData, 'pricing.price_list')),
            'price_source' => self::priceSource(Arr::get($productData, 'pricing.price_source')),
        ])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();

        if ($pricing !== []) {
            $snapshot['pricing'] = $pricing;
        }

        $priceCalculated = self::priceCalculated(Arr::get($productData, 'price_calculated'));

        if ($priceCalculated !== []) {
            $snapshot['price_calculated'] = $priceCalculated;
        }

        return $snapshot;
    }

    public static function priceListSummary(mixed $priceList): ?array
    {
        if (!is_array($priceList)) {
            return null;
        }

        return collect([
            'id' => Arr::get($priceList, 'id'),
            'name' => Arr::get($priceList, 'name'),
            'code' => Arr::get($priceList, 'code'),
            'allow_discounts' => (bool) Arr::get($priceList, 'allow_discounts', true),
        ])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();
    }

    public static function priceSource(mixed $priceSource): ?array
    {
        if (!is_array($priceSource)) {
            return null;
        }

        $snapshot = collect([
            'type' => Arr::get($priceSource, 'type'),
            'inventory_id' => Arr::get($priceSource, 'inventory_id'),
            'price_list_price_id' => Arr::get($priceSource, 'price_list_price_id'),
            'unit_price' => is_numeric(Arr::get($priceSource, 'unit_price'))
                ? (float) Arr::get($priceSource, 'unit_price')
                : null,
            'purchase_price' => is_numeric(Arr::get($priceSource, 'purchase_price'))
                ? (float) Arr::get($priceSource, 'purchase_price')
                : null,
            'price_includes_tax' => (bool) Arr::get($priceSource, 'price_includes_tax', true),
            'is_default' => Arr::has($priceSource, 'is_default')
                ? (bool) Arr::get($priceSource, 'is_default')
                : null,
            'allow_discounts' => (bool) Arr::get($priceSource, 'allow_discounts', true),
            'price_list' => self::priceListSummary(Arr::get($priceSource, 'price_list')),
        ])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();

        if (($snapshot['type'] ?? null) === 'price_list') {
            unset($snapshot['allow_discounts']);
        }

        return $snapshot;
    }

    private static function priceCalculated(mixed $priceCalculated): array
    {
        if (!is_array($priceCalculated)) {
            return [];
        }

        return collect([
            'price' => is_numeric(Arr::get($priceCalculated, 'price'))
                ? (float) Arr::get($priceCalculated, 'price')
                : null,
            'price_final' => is_numeric(Arr::get($priceCalculated, 'price_final'))
                ? (float) Arr::get($priceCalculated, 'price_final')
                : null,
            'purchase_price' => is_numeric(Arr::get($priceCalculated, 'purchase_price'))
                ? (float) Arr::get($priceCalculated, 'purchase_price')
                : null,
            'price_includes_tax' => Arr::has($priceCalculated, 'price_includes_tax')
                ? (bool) Arr::get($priceCalculated, 'price_includes_tax')
                : null,
            'price_list' => self::priceListSummary(Arr::get($priceCalculated, 'price_list')),
            'price_source' => self::priceSource(Arr::get($priceCalculated, 'price_source')),
            'discounts_applied' => is_array(Arr::get($priceCalculated, 'discounts_applied'))
                ? Arr::get($priceCalculated, 'discounts_applied')
                : null,
            'free_gift' => is_array(Arr::get($priceCalculated, 'free_gift'))
                ? Arr::get($priceCalculated, 'free_gift')
                : null,
        ])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();
    }
}
