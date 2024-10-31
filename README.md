# Venditio Core Ecommerce

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pictastudio/venditio-core.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-core)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-core/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pictastudio/venditio-core/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-core/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pictastudio/venditio-core/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pictastudio/venditio-core.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-core)

**Venditio core** it's a headless e-commerce tool.
It provides the core functionality for an e-commerce laravel based application, giving you the freedom to choose the frontend stack.

We offer [**Venditio admin**](https://github.com/pictastudio/venditio-admin) a complementary package that provides an admin panel written with [filamentphp](https://filamentphp.com/)

## Installation

You can install the package via composer:

```bash
composer require pictastudio/venditio-core
```

You can initialize the package with the command below

The command will ask you if you want to use the simple or advanced version, for more information about the differences about the two versions see this [section](#simple-vs-advanced)

```bash
php artisan venditio-core:install
```

## Simple vs Advanced
Advanced has the concept of a `Product` which you can consider as the parent and `ProductItem` which is the final purchasable product.
this difference is useful to accomodate the need for product variants, consider this example:

We have a t-shirt `Product` with id 1 that could have variants in both color and size

Considering these variants:

| size | color |
|------|-------|
| S    | black |
| M    | white |
| L    | red   |

All this variants are computed using a `product_variant` and `product_variant_options` table and then for each possible combination of these variations a `ProductItem` is created (with a `product_id` field to keep the reference to the original product)\
`ProductItem` as written above is the final purchasable item and is the one that should be assigned a unique [sku](https://corporatefinanceinstitute.com/resources/accounting/stock-keeping-unit-sku/#:~:text=A%20stock%20keeping%20unit%20or,and%20more%20efficient%20record%2Dkeeping)

| product_id | size | color |
|------------|------|-------|
| 1          | S    | black |
| 1          | M    | black |
| 1          | L    | black |
| 1          | S    | white |
| 1          | M    | white |
| 1          | L    | white |
| 1          | S    | red   |
| 1          | M    | red   |
| 1          | L    | red   |

## Usage

## Configuration
The first thing to do before using this package is to configure the type you want to use: `Simple` or `Advanced` inside the `AppServiceProvider`

By default the package will use simple but it's always better to explicitate it in your `AppServiceProvider` for clarity
```php
use Illuminate\Support\ServiceProvider;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Packages\Tools\PackageType;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        VenditioCore::packageType(PackageType::Simple);

        // or

        VenditioCore::packageType(PackageType::Advanced);
    }
}
```

### Seeding Data
Add the following seeders to your `DatabaseSeeder` to seed the initial data used by the package, this will seed the countries data as well as a root user, then it will create all the roles and permissions based on the [auth section of the config](#auth)

```php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Database\Seeders\CountrySeeder;
use PictaStudio\VenditioCore\Packages\Simple\Database\Seeders\CurrencySeeder;
use PictaStudio\VenditioCore\Packages\Simple\Database\Seeders\RoleSeeder;
use PictaStudio\VenditioCore\Packages\Simple\Database\Seeders\TaxClassSeeder;
use PictaStudio\VenditioCore\Packages\Simple\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CurrencySeeder::class,
            TaxClassSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
```

### Auth
Add the `HasApiTokens` trait to your user model if not already present and then update the model in the config so the package can use the correct one
Also extend the User model from VenditioCore
```php
namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use PictaStudio\VenditioCore\Packages\Simple\Models\User as VenditioCoreUser;

class User extends VenditioCoreUser
{
    use HasApiTokens;
}

```

then update the class in `config/venditio-core`
```php
'models' => [
    'simple' => [
        // ...
        'user' => App\Models\User::class,
    ],
    // ...
],
```

If you want the package to create a root user then provide inside the `.env` file the following variables
```env
VENDITIO_CORE_ROOT_USER_EMAIL=mail_here
VENDITIO_CORE_ROOT_USER_PASSWORD=password_here
```

```php
/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
|
| Specify the auth manager, roles, resources, actions, and extra permissions
|
*/
'auth' => [
    // 'manager' => AuthManager::class,
    'roles' => [
        'root' => Managers\AuthManager::ROLE_ROOT,
        'admin' => Managers\AuthManager::ROLE_ADMIN,
        'user' => Managers\AuthManager::ROLE_USER,
    ],
    'resources' => [
        'user',
        'role',
        'address',
        'cart',
        'order',
        'product',
        'product-category',
        'brand',
    ],
    'actions' => [
        'view-any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force-delete',
    ],
    'extra_permissions' => [
        // 'orders' => [
        //     'export',
        //     'export-bulk',
        // ],
    ],
    'root_user' => [
        'email' => env('VENDITIO_CORE_ROOT_USER_EMAIL'),
        'password' => env('VENDITIO_CORE_ROOT_USER_PASSWORD'),
    ],
],
```

### Models
Inside the config you will find a section dedicated to models configuration
```php
/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
|
| Specify the models to use
|
*/
'models' => [
    'simple' => [
        'address' => Simple\Models\Address::class,
        'brand' => Simple\Models\Brand::class,
        'cart' => Simple\Models\Cart::class,
        'cart_line' => Simple\Models\CartLine::class,
        'country' => Simple\Models\Country::class,
        'country_tax_class' => Simple\Models\CountryTaxClass::class,
        'currency' => Simple\Models\Currency::class,
        'discount' => Simple\Models\Discount::class,
        'inventory' => Simple\Models\Inventory::class,
        'order' => Simple\Models\Order::class,
        'order_line' => Simple\Models\OrderLine::class,
        'product' => Simple\Models\Product::class,
        'product_category' => Simple\Models\ProductCategory::class,
        'shipping_status' => Simple\Models\ShippingStatus::class,
        'tax_class' => Simple\Models\TaxClass::class,
        'user' => Simple\Models\User::class,
    ],
    'advanced' => [
        'product' => Advanced\Models\Product::class,
        'product_custom_field' => Advanced\Models\ProductCustomField::class,
        'product_item' => Advanced\Models\ProductItem::class,
        'product_type' => Advanced\Models\ProductType::class,
        'product_variant' => Advanced\Models\ProductVariant::class,
        'product_variant_option' => Advanced\Models\ProductVariantOption::class,
    ],
],
```

#### Relations
relations inside models are defined dinamically resolving the correct model namespace from the config, depending if the app is set to use the simple or advanced configuration
```php
// brand relation from Simple\Models\Product model
public function brand(): BelongsTo
{
    return $this->belongsTo(resolve_model('brand'));
}
```

### Validation rules
Validation rules are managed inside separate classes than FormRequests
```php
namespace PictaStudio\VenditioCore\Packages\Simple\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;

class AddressValidation implements AddressValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::enum(config('venditio-core.addresses.type_enum')),
            ],
            'is_default' => 'sometimes|boolean',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            // ...
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'type' => [
                'sometimes',
                'string',
                Rule::enum(config('venditio-core.addresses.type_enum')),
            ],
            'is_default' => 'sometimes|boolean',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            // ...
        ];
    }
}
```
this classes are then resolved out of the container when needed

```php
namespace PictaStudio\VenditioCore\Http\Requests\V1\Address;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('address:create');
    }

    public function rules(AddressValidationRules $addressValidationRules): array
    {
        return $addressValidationRules->getStoreValidationRules();
    }
}
```

Customize validation rules by modifying the class bind in laravel container
```php
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;
use App\Validations\AddressValidation;

// inside AppServiceProvider boot method
public function boot(): void
{
    $this->app->singleton(AddressValidationRules::class, AddressValidation::class);
}
```


### Helper functions
Utility functions used across the package to simplify resolving the correct namespaced classes
```php
function auth_manager(User|Authenticatable|null $user = null): AuthManagerContract
{
    return app(AuthManagerContract::class, ['user' => $user]);
}

/**
 * if the model is not found inside the advanced package, it will fallback to the simple package
 *
 * @param  string  $model  String that identifies the model (one of the keys from config('venditio-core.models'))
 */
function resolve_model(string $model): string
{
    $packageType = VenditioCore::getPackageType();

    return config(
        'venditio-core.models.' . $packageType->value . '.' . $model,
        config('venditio-core.models.simple.' . $model)
    );
}

function query(string $model): Builder
{
    return resolve_model($model)::query();
}

function get_fresh_model_instance(string $model): Model
{
    return new (resolve_model($model));
}

/**
 * if package type is simple the product_item model it's not used
 * so this will resolve product model
 */
function resolve_purchasable_product_model(): string
{
    if (VenditioCore::isSimple()) {
        return resolve_model('product');
    }

    return resolve_model('product_item');
}
```

### Api
#### Routes
Routes are dynamically registered based on the package type configuration
```php
if (VenditioCore::isAdvanced()) {
    Route::apiResource('product_items', ProductItemController::class)->only(['index', 'show']);
}
```

#### Controllers
Controllers that are present both in the simple and advanced version are placed inside `Http\Controllers\Api` then inside the function there's an if statement to determine which controller to hit: the simple (`Packages\Simple\Http\Controllers\Api`) or the advanced one (`Packages\Advanced\Http\Controllers\Api`)
```php
// example of an index method
if (VenditioCore::isSimple()) {
    return app(SimpleProductController::class)->index();
}

return app(AdvancedProductController::class)->index();
```

Controllers that exists only in one version such as `ProductItemController` (which exists only in the advanced version) are hit directly from the `routes/api` file
```php
if (VenditioCore::isAdvanced()) {
    Route::apiResource('product_items', ProductItemController::class)->only(['index', 'show']);
}
```

#### Http Resources
Example of an http resource, with the array key (`product_item.images`) we are telling which attribute we want to mutate and then the closure accepts as a parameter the value of that attribute
You can use dot notation to access attributes because under the hood it uses `Arr::get` and `Arr::set` methods
```php
protected function transformAttributes(): array
{
    return [
        'product_item.images' => fn (?array $images) => (
            collect($images)
                ->map(fn (array $image) => [
                    'alt' => $image['alt'],
                    'img' => asset('storage/' . $image['img']),
                ])
                ->toArray()
        ),
        'product_item.files' => fn (?array $files) => (
            collect($files)
                ->map(fn (array $file) => [
                    'name' => $file['name'],
                    'file' => asset('storage/' . $file['file']),
                ])
                ->toArray()
        ),
    ];
}
```

### Cart
#### Generator
Customize cart identifier generator by modifying the binding in laravel container
```php
$this->app->singleton(CartIdentifierGeneratorInterface::class, CartIdentifierGenerator::class);
```

### Order
#### Generator
Customize order identifier generator by modifying the binding in laravel container
```php
$this->app->singleton(OrderIdentifierGeneratorInterface::class, OrderIdentifierGenerator::class);
```
Example of custom generator class
```php
namespace App\Generators;

use PictaStudio\VenditioCore\Packages\Simple\Models\Order;
use PictaStudio\VenditioCore\Orders\Contracts\OrderIdentifierGeneratorInterface;

class OrderIdentifierGenerator implements OrderIdentifierGeneratorInterface
{
    public function generate(Order $order): string
    {
        // implement your custom logic here
    }
}
```

## Structure
```
// folder structure

Helpers
    |--- Functions
packages/
|--- simple // business logic specific for simple ecommerce
    |--- Actions
    |--- Models
        |--- Scopes
        |--- Traits
    |--- Pipelines
    |--- Services
    |--- Database
        |--- Migrations
    |--- ...
|--- advanced // business logic specific for advanced ecommerce
    |--- Actions
    |--- Models
        |--- Scopes
        |--- Traits
    |--- Pipelines
    |--- Services
    |--- Database
        |--- Migrations
    |--- ...
```

TODO:
- [ ] update outdated docs
- [ ] docs on global available helpers
- [ ] pipelines docs
- [ ] docs on `OrderStatus` enum and `Contracts\OrderStatus` on how it's used and the logic behind it
- [ ] fix updating cart lines in `CartUpdatePipeline` (add line/s and recalculate cart totals)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Picta Studio](https://github.com/pictastudio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
