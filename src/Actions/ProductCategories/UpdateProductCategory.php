<?php

namespace PictaStudio\Venditio\Actions\ProductCategories;

use PictaStudio\Venditio\Models\ProductCategory;

class UpdateProductCategory
{
    public function handle(ProductCategory $category, array $payload): ProductCategory
    {
        $category->fill($payload);
        $category->save();

        return $category->refresh();
    }
}
