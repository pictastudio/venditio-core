<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\CartLine;

class CartLineSeeder extends Seeder
{
    public function run(): void
    {
        CartLine::factory()->count(10)->create();
    }
}
