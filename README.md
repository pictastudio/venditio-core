# Venditio Core Ecommerce

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pictastudio/venditio-core.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-core)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-core/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pictastudio/venditio-core/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-core/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pictastudio/venditio-core/actions?query=workflow%3A)
[![Total Downloads](https://img.shields.io/packagist/dt/pictastudio/venditio-core.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-core)

**Venditio core** it's a headless e-commerce tool.
It provides the core functionality for an e-commerce laravel based application, giving you the freedom to choose the frontend stack.

We offer [**Venditio admin**](https://github.com/pictastudio/venditio-admin) a complementary package that provides an admin panel written with [filamentphp](https://filamentphp.com/)

## Installation

You can install the package via composer:

```bash
composer require pictastudio/venditio-core
```

## Product Variants

Venditio Core models product variants by treating a base `Product` as the parent and variant products as the purchasable items.
This allows you to represent multiple option combinations while keeping a single product identity.

We have a t-shirt `Product` with id 1 that could have variants in both color and size

Considering these variants:

| size | color |
| ---- | ----- |
| S    | black |
| M    | white |
| L    | red   |

All the variants are computed using `product_variants` and `product_variant_options`. Each concrete variant is stored as a `products` row with a `parent_id` that points to the base product. The variant row is the purchasable item and is the one that should be assigned a unique [sku](https://corporatefinanceinstitute.com/resources/accounting/stock-keeping-unit-sku/#:~:text=A%20stock%20keeping%20unit%20or,and%20more%20efficient%20record%2Dkeeping).

| product_id | size | color |
| ---------- | ---- | ----- |
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

## Documentation

- Architecture and package design: `docs/ARCHITECTURE.md`
- API reference and examples: `docs/API.md`

## Configuration

No edition or mode selection is required. All behavior is configured via the `venditio-core` config file.

### Seeding Data

Add the following seeders to your `DatabaseSeeder` to seed the initial data used by the package, this will seed the countries data as well as a root user, then it will create all the roles and permissions based on the [auth section of the config](#auth)

```php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Database\Seeders\CountrySeeder;
use PictaStudio\VenditioCore\Database\Seeders\CurrencySeeder;
use PictaStudio\VenditioCore\Database\Seeders\RoleSeeder;
use PictaStudio\VenditioCore\Database\Seeders\TaxClassSeeder;
use PictaStudio\VenditioCore\Database\Seeders\UserSeeder;

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
use PictaStudio\VenditioCore\Models\User as VenditioCoreUser;

class User extends VenditioCoreUser
{
    use HasApiTokens;
}

```

then update the class in `config/venditio-core`

```php
'models' => [
    // ...
    'user' => App\Models\User::class,
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
    'product_custom_field' => Advanced\Models\ProductCustomField::class,
    'product_type' => Advanced\Models\ProductType::class,
    'product_variant' => Advanced\Models\ProductVariant::class,
    'product_variant_option' => Advanced\Models\ProductVariantOption::class,
],
```

#### Relations

Relations inside models are defined dynamically by resolving the configured model class from the config.

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
namespace PictaStudio\VenditioCore\Validations;

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

### Dto

The package uses Dtos inside the pipelines and you can modify the class it uses inside the the config file
The important thing is that those classes need to implement the provided interfaces

for Order dto: PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract
for Cart dto: PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract

### Helper functions

Utility functions used across the package to simplify resolving the correct namespaced classes

```php
function auth_manager(User|Authenticatable|null $user = null): AuthManagerContract
{
    return app(AuthManagerContract::class, ['user' => $user]);
}

/**
 * @param  string  $model  String that identifies the model (one of the keys from config('venditio-core.models'))
 */
function resolve_model(string $model): string
{
    return config('venditio-core.models.' . $model);
}

function query(string $model): Builder
{
    return resolve_model($model)::query();
}

function get_fresh_model_instance(string $model): Model
{
    return new (resolve_model($model));
}
```

### Api

#### Routes

Routes are registered once, without runtime branching

```php
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
```

#### Controllers

Controllers live directly under `Http\Controllers\Api` and do not switch at runtime.

#### Http Resources

Example of an http resource, with the array key (`product.images`) we are telling which attribute we want to mutate and then the closure accepts as a parameter the value of that attribute
You can use dot notation to access attributes because under the hood it uses `Arr::get` and `Arr::set` methods

```php
protected function transformAttributes(): array
{
    return [
        'product.images' => fn (?array $images) => (
            collect($images)
                ->map(fn (array $image) => [
                    'alt' => $image['alt'],
                    'img' => asset('storage/' . $image['img']),
                ])
                ->toArray()
        ),
        'product.files' => fn (?array $files) => (
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

use PictaStudio\VenditioCore\Models\Order;
use PictaStudio\VenditioCore\Orders\Contracts\OrderIdentifierGeneratorInterface;

class OrderIdentifierGenerator implements OrderIdentifierGeneratorInterface
{
    public function generate(Order $order): string
    {
        // implement your custom logic here
    }
}
```

### Commands

the package provides some console commands to deal with common use cases

#### Carts

- ReleaseStockForAbandonedCarts
  checks all carts with a pending status which by default are `processing` and `active`
  pending statuses are customizable by changing the `getPendingStatuses()` function in `CartStatus` enum which is located in the config file under `carts.status_enum`

#### Bruno API Collection

- PublishBrunoCollection
  publishes the Bruno request collection into the host app at `bruno/venditio-core`

```bash
php artisan vendor:publish --tag=venditio-core-bruno
```

## Structure

```
// folder structure (high level)

src/
|--- Actions
|--- Contracts
|--- Helpers
|--- Http
|--- Packages
    |--- Simple   // core models, enums, validations, factories (internal module)
    |--- Advanced // variant system models, validations, factories (internal module)
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
