<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        ProductVariant::factory()->count(10)->create();
    }
}
