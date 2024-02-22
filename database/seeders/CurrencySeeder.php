<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\Country;
use PictaStudio\VenditioCore\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        // Currency::factory()
        //     ->for(Country::where('iso_2', 'IT')->first())
        //     ->create([
        //         'name' => 'Euro',
        //         'code' => 'EUR',
        //         'symbol' => '€',
        //         'exchange_rate' => 1,
        //         'enabled' => true,
        //         'default' => true,
        //     ]);

        Currency::query()
            ->create([
                'country_id' => Country::where('iso_2', 'IT')->value('id'),
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'exchange_rate' => 1,
                'enabled' => true,
                'default' => true,
            ]);
    }
}
