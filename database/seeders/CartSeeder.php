<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\Cart;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        Cart::factory()->count(10)->create();
    }
}
