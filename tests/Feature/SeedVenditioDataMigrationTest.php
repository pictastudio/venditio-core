<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\Venditio\Models\Country;

uses(RefreshDatabase::class);

it('seeds countries with nullable fields from the migration', function () {
    $migration = include __DIR__ . '/../../database/migrations/seed_venditio_data.php';
    $seedCountries = new ReflectionMethod($migration, 'seedCountries');

    $seedCountries->invoke($migration);

    expect(Country::query()->count())->toBe(250)
        ->and(Country::query()->where('iso_2', 'AQ')->first())
        ->capital->toBe('');
});
