<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\ProductVariantOption;

class ProductVariantOptionSeeder extends Seeder
{
    public function run(): void
    {
        ProductVariantOption::factory()->count(10)->create();
    }
}
