<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        Cart::factory()->count(10)->create();
    }
}
