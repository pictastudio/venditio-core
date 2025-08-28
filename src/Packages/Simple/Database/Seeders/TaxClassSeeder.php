<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\{Country, TaxClass};

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
