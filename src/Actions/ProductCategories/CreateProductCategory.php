<?php

namespace PictaStudio\VenditioCore\Actions\ProductCategories;

use PictaStudio\VenditioCore\Models\ProductCategory;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CreateProductCategory
{
    public function handle(array $payload): ProductCategory
    {
        /** @var ProductCategory $category */
        $category = resolve_model('product_category')::create($payload);

        return $category->refresh();
    }
}
