<?php

namespace PictaStudio\VenditioCore;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use PictaStudio\VenditioCore\Contracts\{CartIdentifierGeneratorInterface, DiscountCalculatorInterface, DiscountUsageRecorderInterface, DiscountablesResolverInterface, OrderIdentifierGeneratorInterface};
use PictaStudio\VenditioCore\Discounts\{DiscountCalculator, DiscountUsageRecorder, DiscountablesResolver};
use PictaStudio\VenditioCore\Dto\{CartDto, CartLineDto, OrderDto};
use PictaStudio\VenditioCore\Dto\Contracts\{CartDtoContract, CartLineDtoContract, OrderDtoContract};
use PictaStudio\VenditioCore\Facades\VenditioCore as VenditioCoreFacade;
use PictaStudio\VenditioCore\Generators\{CartIdentifierGenerator, OrderIdentifierGenerator};
use PictaStudio\VenditioCore\Managers\AuthManager;
use PictaStudio\VenditioCore\Managers\Contracts\AuthManager as AuthManagerContract;
use PictaStudio\VenditioCore\Models\User;
use PictaStudio\VenditioCore\Validations\{AddressValidation, CartLineValidation, CartValidation, OrderValidation, ProductCategoryValidation, ProductTypeValidation, ProductValidation, ProductVariantOptionValidation, ProductVariantValidation};
use PictaStudio\VenditioCore\Validations\Contracts\{AddressValidationRules, CartLineValidationRules, CartValidationRules, OrderValidationRules, ProductCategoryValidationRules, ProductTypeValidationRules, ProductValidationRules, ProductVariantOptionValidationRules, ProductVariantValidationRules};
use Spatie\LaravelPackageTools\{Package, PackageServiceProvider};

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
            ->hasMigrations([
                'create_addresses_table',
                'create_countries_table',
                'create_country_tax_class_table',
                'create_tax_classes_table',
                'create_currencies_table',
                'create_orders_table',
                'create_order_lines_table',
                'create_shipping_statuses_table',
                'create_brands_table',
                'create_product_categories_table',
                'create_discount_applications_table',
                'create_discounts_table',
                'create_products_table',
                'create_product_types_table',
                'create_product_category_product_table',
                'create_product_variants_table',
                'create_product_custom_fields_table',
                'create_product_variant_options_table',
                'create_product_configuration_table',
                'create_inventories_table',
                'create_carts_table',
                'create_cart_lines_table',
            ]);
        // ->hasRoute('api');
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
        $this->bindDiscountClasses();

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

    private function bindDiscountClasses(): void
    {
        $this->app->singleton(
            DiscountCalculatorInterface::class,
            config('venditio-core.discounts.calculator', DiscountCalculator::class)
        );

        $this->app->singleton(
            DiscountablesResolverInterface::class,
            config('venditio-core.discounts.discountables_resolver', DiscountablesResolver::class)
        );

        $this->app->singleton(
            DiscountUsageRecorderInterface::class,
            config('venditio-core.discounts.usage_recorder', DiscountUsageRecorder::class)
        );
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
            ->name(mb_rtrim(config('venditio-core.routes.api.v1.name'), '.') . '.')
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
        $morphMap = collect(config('venditio-core.models', []))
            ->filter(fn (mixed $model) => is_string($model) && class_exists($model))
            ->toArray();

        if (!isset($morphMap['user'])) {
            $morphMap['user'] = User::class;
        }

        Relation::morphMap($morphMap);
    }
}
