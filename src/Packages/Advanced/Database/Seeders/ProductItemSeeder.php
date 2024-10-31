<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductItem;

class ProductItemSeeder extends Seeder
{
    public function run(): void
    {
        ProductItem::factory()->count(10)->create();
    }
}
