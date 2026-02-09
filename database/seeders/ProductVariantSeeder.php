<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        ProductVariant::factory()->count(10)->create();
    }
}
