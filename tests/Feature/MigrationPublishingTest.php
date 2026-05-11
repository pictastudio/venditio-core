<?php

use Illuminate\Support\ServiceProvider;
use PictaStudio\Venditio\VenditioServiceProvider;

it('makes every venditio migration publishable', function () {
    $migrationNames = collect(glob(__DIR__ . '/../../database/migrations/*.php'))
        ->map(fn (string $path) => pathinfo($path, PATHINFO_FILENAME))
        ->sort()
        ->values();

    $publishableMigrationNames = collect(ServiceProvider::pathsToPublish(
        VenditioServiceProvider::class,
        'venditio-migrations'
    ))
        ->keys()
        ->map(fn (string $path) => pathinfo($path, PATHINFO_FILENAME))
        ->sort()
        ->values();

    expect($publishableMigrationNames)->toEqual($migrationNames);
});
