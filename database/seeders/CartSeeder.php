<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\Cart;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        Cart::factory()->count(10)->create();
    }
}
