<?php

namespace PictaStudio\VenditioCore\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as Orchestra;
use PictaStudio\VenditioCore\VenditioCoreServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => str($modelName)
                ->replace('Models', 'Database\\Factories')
                ->append('Factory')
                ->toString()
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
