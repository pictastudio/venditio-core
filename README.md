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

```php
$venditioCore = new PictaStudio\VenditioCore();
echo $venditioCore->echoPhrase('Hello, PictaStudio!');
```

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
