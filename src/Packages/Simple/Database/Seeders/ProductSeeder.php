<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;
use PictaStudio\VenditioCore\Packages\Simple\Models\Inventory;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;
use PictaStudio\VenditioCore\Packages\Simple\Models\TaxClass;

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
