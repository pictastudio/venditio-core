<?php

namespace PictaStudio\VenditioCore\Actions\Products;

use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;

class UpdateProduct
{
    public function handle(Product $product, array $payload): Product
    {
        $categoryIdsProvided = array_key_exists('category_ids', $payload);
        $categoryIds = Arr::pull($payload, 'category_ids', []);

        $product->fill($payload);
        $product->save();

        if ($categoryIdsProvided) {
            $product->categories()->sync($categoryIds ?? []);
        }

        return $product->refresh();
    }
}
