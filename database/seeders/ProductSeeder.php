<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\Brand;
use PictaStudio\VenditioCore\Models\Inventory;
use PictaStudio\VenditioCore\Models\Product;
use PictaStudio\VenditioCore\Models\TaxClass;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory()
            // ->for(
            //     Brand::factory()
            //         ->count(1)
            // )
            // ->for(
            //     TaxClass::factory()
            //         ->count(1)
            // )
            ->has(
                Inventory::factory()
                    ->count(1)
            )
            ->count(100)
            ->create();
    }
}
