<?php

namespace PictaStudio\Venditio\Actions\ProductCategories;

use PictaStudio\Venditio\Models\ProductCategory;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class CreateProductCategory
{
    public function handle(array $payload): ProductCategory
    {
        /** @var ProductCategory $category */
        $category = resolve_model('product_category')::create($payload);

        return $category->refresh();
    }
}
