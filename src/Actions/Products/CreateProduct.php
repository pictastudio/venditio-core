<?php

namespace PictaStudio\VenditioCore\Actions\Products;

use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Models\Product;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CreateProduct
{
    public function handle(array $payload): Product
    {
        $categoryIds = Arr::pull($payload, 'category_ids', []);

        /** @var Product $product */
        $product = resolve_model('product')::create($payload);

        if (!empty($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }

        return $product->refresh();
    }
}
