<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        ProductCategory::factory()
            ->count(10)
            ->create();
    }
}
