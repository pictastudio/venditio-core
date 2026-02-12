<?php

namespace PictaStudio\Venditio\Actions\Products;

use Illuminate\Support\Arr;
use PictaStudio\Venditio\Models\Product;

class UpdateProduct
{
    public function handle(Product $product, array $payload): Product
    {
        $categoryIdsProvided = array_key_exists('category_ids', $payload);
        $categoryIds = Arr::pull($payload, 'category_ids', []);
        $inventoryProvided = array_key_exists('inventory', $payload);
        $inventoryPayload = Arr::pull($payload, 'inventory');

        $product->fill($payload);
        $product->save();

        if ($categoryIdsProvided) {
            $product->categories()->sync($categoryIds ?? []);
        }

        if ($inventoryProvided && is_array($inventoryPayload)) {
            $product->inventory()->updateOrCreate(
                ['product_id' => $product->getKey()],
                $inventoryPayload
            );
        }

        return $product->refresh()->load(['inventory', 'variantOptions']);
    }
}
