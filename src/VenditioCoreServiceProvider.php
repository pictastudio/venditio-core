<?php

namespace PictaStudio\VenditioCore;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VenditioCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $migrations = collect(scandir($package->basePath('/../database/migrations')))
            ->reject(fn (string $file) => in_array($file, ['.', '..']))
            ->map(fn (string $file) => str($file)->beforeLast('.php'))
            ->toArray();

        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('venditio-core')
            ->hasConfigFile()
            ->hasMigrations($migrations)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToRunMigrations();
            });
    }
}
