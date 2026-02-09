<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\Country;
use PictaStudio\VenditioCore\Models\TaxClass;

class TaxClassSeeder extends Seeder
{
    public function run(): void
    {
        // TaxClass::factory()
        //     ->hasAttached(
        //         Country::factory(),
        //         [
        //             'rate' => 22,
        //         ],
        //     )
        //     ->create([
        //         'name' => 'Standard',
        //     ]);

        TaxClass::create([
            'name' => 'Standard',
        ]);
    }
}
