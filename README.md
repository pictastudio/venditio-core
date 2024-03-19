# ecommerce core package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pictastudio/venditio-core.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-core)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-core/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pictastudio/venditio-core/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-core/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pictastudio/venditio-core/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pictastudio/venditio-core.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-core)

**Venditio core** it's a headless e-commerce tool.
It provides the core functionality for an e-commerce laravel based application, giving you the freedom to choose the frontend stack.

## Installation

You can install the package via composer:

```bash
composer require pictastudio/venditio-core
```

You can install the package with:

```bash
php artisan venditio-core:install
```

This is the contents of the published config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    |
    | Specify the auth manager
    |
    */
    'auth' => [
        'manager' => AuthManager::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Specify the models to use
    |
    */
    'models' => [
        'address' => PictaStudio\VenditioCore\Models\Address::class,
        'brand' => PictaStudio\VenditioCore\Models\Brand::class,
        'cart' => PictaStudio\VenditioCore\Models\Cart::class,
        'cart_line' => PictaStudio\VenditioCore\Models\CartLine::class,
        'country' => PictaStudio\VenditioCore\Models\Country::class,
        'country_tax_class' => PictaStudio\VenditioCore\Models\CountryTaxClass::class,
        'currency' => PictaStudio\VenditioCore\Models\Currency::class,
        'discount' => PictaStudio\VenditioCore\Models\Discount::class,
        'inventory' => PictaStudio\VenditioCore\Models\Inventory::class,
        'order' => PictaStudio\VenditioCore\Models\Order::class,
        'order_line' => PictaStudio\VenditioCore\Models\OrderLine::class,
        'product' => PictaStudio\VenditioCore\Models\Product::class,
        'product_category' => PictaStudio\VenditioCore\Models\ProductCategory::class,
        'product_custom_field' => PictaStudio\VenditioCore\Models\ProductCustomField::class,
        'product_type' => PictaStudio\VenditioCore\Models\ProductType::class,
        'product_item' => PictaStudio\VenditioCore\Models\ProductItem::class,
        'product_variant' => PictaStudio\VenditioCore\Models\ProductVariant::class,
        'product_variant_option' => PictaStudio\VenditioCore\Models\ProductVariantOption::class,
        'shipping_status' => PictaStudio\VenditioCore\Models\ShippingStatus::class,
        'tax_class' => PictaStudio\VenditioCore\Models\TaxClass::class,
        'user' => PictaStudio\VenditioCore\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing
    |--------------------------------------------------------------------------
    |
    | Specify the pricing formatter
    |
    */
    'pricing' => [
        'formatter' => DefaultPriceFormatter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Decimal
    |--------------------------------------------------------------------------
    |
    | Specify the decimal formatter
    |
    */
    'decimal' => [
        'formatter' => DefaultDecimalFormatter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes configuration
    |
    */
    'scopes' => [
        'in_date_range' => [
            'allow_null' => true, // allow null values to pass when checking date range
            'include_start_date' => true, // include the start date in the date range
            'include_end_date' => true, // include the end date in the date range
        ],
    ],
];
```

## Usage

### Api
By default the api routes provided by this package are public, with no authetication setup.
You should provide the authentication layer in your app using something like `laravel/breeze` for SPA or other kinds of authentication.

### Carts
#### Generator
Customize cart identifier generator
Modify the bind in laravel container
```php
$this->app->bind(CartIdentifierGeneratorInterface::class, CartIdentifierGenerator::class);
```

### Orders
#### Generator
Customize order identifier generator
Modify the bind in laravel container
```php
$this->app->bind(OrderIdentifierGeneratorInterface::class, OrderIdentifierGenerator::class);
```
```php
namespace App\Generators;

use PictaStudio\VenditioCore\Models\Order;
use PictaStudio\VenditioCore\Orders\Contracts\OrderIdentifierGeneratorInterface;

class MyCustomGenerator implements OrderIdentifierGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(Order $order): string
    {
        // ...
        return 'my-custom-reference';
    }
}
```

TODO:
- [ ] update outdated docs
- [ ] docs on binding of models and dtos into container
- [ ] pipeline docs
- [ ] docs on `OrderStatus` enum and `Contracts\OrderStatus` on how it's used and the logic behind it
- [ ] docs on customizing validation rules
- [ ] fix updating cart lines in `CartUpdatePipeline`

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
