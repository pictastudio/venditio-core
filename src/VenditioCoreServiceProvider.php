<?php

namespace PictaStudio\VenditioCore;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use PictaStudio\VenditioCore\Contracts\CartIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Contracts\OrderIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Dto\CartDto;
use PictaStudio\VenditioCore\Dto\CartLineDto;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract;
use PictaStudio\VenditioCore\Dto\OrderDto;
use PictaStudio\VenditioCore\Facades\VenditioCore as VenditioCoreFacade;
use PictaStudio\VenditioCore\Generators\CartIdentifierGenerator;
use PictaStudio\VenditioCore\Generators\OrderIdentifierGenerator;
use PictaStudio\VenditioCore\Managers\AuthManager;
use PictaStudio\VenditioCore\Managers\Contracts\AuthManager as AuthManagerContract;
use PictaStudio\VenditioCore\Models\User;
use PictaStudio\VenditioCore\Validations\AddressValidation;
use PictaStudio\VenditioCore\Validations\CartLineValidation;
use PictaStudio\VenditioCore\Validations\CartValidation;
use PictaStudio\VenditioCore\Validations\OrderValidation;
use PictaStudio\VenditioCore\Validations\ProductCategoryValidation;
use PictaStudio\VenditioCore\Validations\ProductValidation;
use PictaStudio\VenditioCore\Validations\ProductTypeValidation;
use PictaStudio\VenditioCore\Validations\ProductVariantValidation;
use PictaStudio\VenditioCore\Validations\ProductVariantOptionValidation;
use PictaStudio\VenditioCore\Packages\Tools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\{Package, PackageServiceProvider};
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\CartLineValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\OrderValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\ProductCategoryValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\ProductValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\ProductTypeValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\ProductVariantValidationRules;
use PictaStudio\VenditioCore\Validations\Contracts\ProductVariantOptionValidationRules;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

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
            ->hasMigrations();
            // ->hasRoute('api');
    }

    public function newPackage(): Package
    {
        return new Package;
    }

    public function registeringPackage()
    {
        $this->app->bind('venditio-core', fn (Application $app) => (
            $app->make(VenditioCore::class)
        ));
    }

    public function packageBooted(): void
    {
        $this->registerApiRoutes();
        $this->bindValidationClasses();
        // $this->bindDtos();
        $this->registerFactoriesGuessing();
        $this->registerMorphMap();

        $this->app->singleton(AuthManagerContract::class, fn () => (
            AuthManager::make(fn () => auth()->guard()->user())
        ));

        $this->app->singleton(CartIdentifierGeneratorInterface::class, CartIdentifierGenerator::class);
        $this->app->singleton(OrderIdentifierGeneratorInterface::class, OrderIdentifierGenerator::class);
    }

    private function bindValidationClasses(): void
    {
        $validations = [
            AddressValidationRules::class => AddressValidation::class,
            CartValidationRules::class => CartValidation::class,
            CartLineValidationRules::class => CartLineValidation::class,
            OrderValidationRules::class => OrderValidation::class,
            ProductValidationRules::class => ProductValidation::class,
            ProductCategoryValidationRules::class => ProductCategoryValidation::class,
            ProductTypeValidationRules::class => ProductTypeValidation::class,
            ProductVariantValidationRules::class => ProductVariantValidation::class,
            ProductVariantOptionValidationRules::class => ProductVariantOptionValidation::class,
        ];

        foreach ($validations as $contract => $implementation) {
            $this->app->singleton($contract, $implementation);
        }
    }

    // private function bindDtos(): void
    // {
    //     $this->app->singleton(OrderDtoContract::class, fn (Application $app) => OrderDto::bindIntoContainer());
    //     $this->app->singleton(CartDtoContract::class, fn (Application $app) => CartDto::bindIntoContainer());
    //     $this->app->singleton(CartLineDtoContract::class, fn (Application $app) => CartLineDto::bindIntoContainer());
    // }

    private function registerApiRoutes(): void
    {
        if (!config('venditio-core.routes.api.enable')) {
            return;
        }

        if (!config('venditio-core.routes.api.json_resource_enable_wrapping')) {
            JsonResource::withoutWrapping();
        }

        if (config('venditio-core.policies.register')) {
            VenditioCoreFacade::registerPolicies();
        }

        $prefix = config('venditio-core.routes.api.v1.prefix');

        // VenditioCore::configureRateLimiting($prefix);
        // config('venditio-core.routes.api.v1.rate_limit.configure')();

        VenditioCoreFacade::configureRateLimiting($prefix);

        Route::middleware(config('venditio-core.routes.api.v1.middleware'))
            ->prefix($prefix)
            ->name(rtrim(config('venditio-core.routes.api.v1.name'), '.') . '.')
            ->group(fn () => (
                $this->loadRoutesFrom($this->package->basePath('/../routes/v1/api.php'))
            ));
    }

    private function registerFactoriesGuessing(): void
    {
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => str($modelName)
                ->replace('Models', 'Database\\Factories')
                ->append('Factory')
                ->toString()
        );
    }

    private function registerMorphMap(): void
    {
        $morphMap = [
            'user' => User::class,
        ];

        $productModel = resolve_model('product');
        $morphMap['product'] = $productModel;

        Relation::morphMap($morphMap);
    }
}
