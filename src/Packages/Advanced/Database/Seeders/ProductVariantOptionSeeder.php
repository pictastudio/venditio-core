<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariantOption;

class ProductVariantOptionSeeder extends Seeder
{
    public function run(): void
    {
        ProductVariantOption::factory()->count(10)->create();
    }
}
