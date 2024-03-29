<?php

namespace PictaStudio\VenditioCore;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use PictaStudio\VenditioCore\Dto\CartDto;
use PictaStudio\VenditioCore\Dto\CartLineDto;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract;
use PictaStudio\VenditioCore\Dto\OrderDto;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Helpers\Cart\Contracts\CartIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Helpers\Cart\Generators\CartIdentifierGenerator;
use PictaStudio\VenditioCore\Helpers\Order\Contracts\OrderIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Helpers\Order\Generators\OrderIdentifierGenerator;
use PictaStudio\VenditioCore\Managers\AuthManager;
use PictaStudio\VenditioCore\Managers\Contracts\AuthManager as AuthManagerContract;
use PictaStudio\VenditioCore\VenditioCore as VenditioCoreClass;
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

    // public function registeringPackage()
    // {
    //     $this->app->bind('venditio-core', function (Application $app) {
    //         return $app->make(VenditioCore::class);
    //     });
    // }

    public function packageBooted(): void
    {
        $this->registerApiRoutes();
        $this->bindModels();
        $this->bindValidationClasses();
        $this->bindDtos();

        $this->app->singleton(AuthManagerContract::class, fn (Application $app) => (
            AuthManager::make($app->make('auth')->user())
        ));

        $this->app->singleton(CartIdentifierGeneratorInterface::class, CartIdentifierGenerator::class);
        $this->app->singleton(OrderIdentifierGeneratorInterface::class, OrderIdentifierGenerator::class);
    }

    private function bindModels(): void
    {
        foreach (config('venditio-core.models') as $contract => $implementation) {
            $this->app->singleton($contract, $implementation);
        }
    }

    private function bindValidationClasses(): void
    {
        foreach (config('venditio-core.validations') as $contract => $implementation) {
            $this->app->singleton($contract, $implementation);
        }
    }

    private function bindDtos(): void
    {
        $this->app->singleton(OrderDtoContract::class, fn (Application $app) => OrderDto::bindIntoContainer());
        $this->app->singleton(CartDtoContract::class, fn (Application $app) => CartDto::bindIntoContainer());
        $this->app->singleton(CartLineDtoContract::class, fn (Application $app) => CartLineDto::bindIntoContainer());
    }

    private function registerApiRoutes(): void
    {
        if (!config('venditio-core.routes.api.enable')) {
            return;
        }

        if (!config('venditio-core.routes.api.json_resource_enable_wrapping')) {
            JsonResource::withoutWrapping();
        }

        $this->registerPolicies();

        $prefix = config('venditio-core.routes.api.v1.prefix');

        // VenditioCore::configureRateLimiting($prefix);
        // config('venditio-core.routes.api.v1.rate_limit.configure')();

        VenditioCoreClass::configureRateLimiting($prefix);

        Route::middleware(config('venditio-core.routes.api.v1.middleware'))
            ->prefix($prefix)
            ->name(rtrim(config('venditio-core.routes.api.v1.name'), '.') . '.')
            ->group(fn () => (
                $this->loadRoutesFrom($this->package->basePath('/../routes/v1/api.php'))
            ));
    }

    private function registerPolicies(): void
    {
        foreach (config('venditio-core.models') as $contract => $model) {
            $model = class_basename($model);

            if (!class_exists("PictaStudio\VenditioCore\\Policies\\{$model}Policy")) {
                continue;
            }

            Gate::policy(
                "PictaStudio\VenditioCore\Models\\{$model}",
                "PictaStudio\VenditioCore\Policies\\{$model}Policy"
            );
        }
    }
}
