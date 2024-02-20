<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\ProductItem;

class ProductItemSeeder extends Seeder
{
    public function run(): void
    {
        ProductItem::factory()->count(10)->create();
    }
}
