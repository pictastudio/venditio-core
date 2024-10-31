<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use PictaStudio\VenditioCore\Packages\Simple\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = File::json(__DIR__ . '/data/countries.json');

        $countries = collect($countries)
            ->map(function (array $country) {
                return array_merge($country, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })
            ->toArray();

        Country::unguard();

        Country::insert($countries);

        Country::reguard();
    }
}
