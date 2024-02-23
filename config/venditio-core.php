<?php

use PictaStudio\VenditioCore\Formatters\Decimal\DefaultDecimalFormatter;
use PictaStudio\VenditioCore\Formatters\Pricing\DefaultPriceFormatter;
use PictaStudio\VenditioCore\Managers\AuthManager;

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
