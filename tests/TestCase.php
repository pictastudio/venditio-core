<?php

namespace PictaStudio\VenditioCore\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as Orchestra;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Packages\Tools\PackageType;
use PictaStudio\VenditioCore\VenditioCoreServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $baseNamespace = match (VenditioCore::getPackageType()) {
            PackageType::Simple => 'PictaStudio\\VenditioCore\\Packages\\Simple\\Database\\Factories',
            PackageType::Advanced => 'PictaStudio\\VenditioCore\\Packages\\Advanced\\Database\\Factories',
        };

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => $baseNamespace . '\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            VenditioCoreServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // $migration = include __DIR__.'/../database/migrations/create_venditio-core_table.php.stub';
        // $migration->up();

        // $migrations = collect(scandir(__DIR__ . '/../database/migrations'))
        //     ->reject(fn (string $file) => in_array($file, ['.', '..']))
        //     ->map(fn (string $file) => str($file)->beforeLast('.php'))
        //     ->toArray();

        // Artisan::call('migrate', ['--path' => 'database/migrations']);
    }
}
