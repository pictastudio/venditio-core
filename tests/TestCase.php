<?php

namespace PictaStudio\Venditio\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase as Orchestra;
use PictaStudio\Venditio\VenditioServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'PictaStudio\\Venditio\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // $migration = include __DIR__.'/../database/migrations/create_venditio_table.php.stub';
        // $migration->up();

        // $migrations = collect(scandir(__DIR__ . '/../database/migrations'))
        //     ->reject(fn (string $file) => in_array($file, ['.', '..']))
        //     ->map(fn (string $file) => str($file)->beforeLast('.php'))
        //     ->toArray();

        // Artisan::call('migrate', ['--path' => 'database/migrations']);
    }

    protected function getPackageProviders($app)
    {
        return [
            VenditioServiceProvider::class,
        ];
    }
}
