<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        ProductVariant::factory()->count(10)->create();
    }
}
