<?php

namespace PictaStudio\VenditioCore\Actions\ProductCategories;

use PictaStudio\VenditioCore\Models\ProductCategory;

class UpdateProductCategory
{
    public function handle(ProductCategory $category, array $payload): ProductCategory
    {
        $category->fill($payload);
        $category->save();

        return $category->refresh();
    }
}
