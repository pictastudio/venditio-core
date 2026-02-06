<?php

namespace PictaStudio\VenditioCore\Actions\ProductCategories;

use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;

class UpdateProductCategory
{
    public function handle(ProductCategory $category, array $payload): ProductCategory
    {
        $category->fill($payload);
        $category->save();

        return $category->refresh();
    }
}
