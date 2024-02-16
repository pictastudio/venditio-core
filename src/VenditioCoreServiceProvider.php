<?php

namespace PictaStudio\VenditioCore;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use PictaStudio\VenditioCore\Commands\VenditioCoreCommand;

class VenditioCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('venditio-core')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_venditio-core_table')
            ->hasCommand(VenditioCoreCommand::class);
    }
}
