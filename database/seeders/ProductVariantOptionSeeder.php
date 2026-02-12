<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\ProductVariantOption;

class ProductVariantOptionSeeder extends Seeder
{
    public function run(): void
    {
        ProductVariantOption::factory()->count(10)->create();
    }
}
