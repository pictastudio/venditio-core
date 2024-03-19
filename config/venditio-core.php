<?php

use PictaStudio\VenditioCore\Enums;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Formatters\Decimal\DefaultDecimalFormatter;
use PictaStudio\VenditioCore\Formatters\Pricing\DefaultPriceFormatter;
use PictaStudio\VenditioCore\Managers;
use PictaStudio\VenditioCore\Models;
use PictaStudio\VenditioCore\Pipelines\Cart;
use PictaStudio\VenditioCore\Pipelines\CartLine;
use PictaStudio\VenditioCore\Pipelines\Order;
use PictaStudio\VenditioCore\Validations;

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

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Specify the models to use
    |
    */
    'models' => [
        Models\Contracts\Address::class => Models\Address::class,
        Models\Contracts\Brand::class => Models\Brand::class,
        Models\Contracts\Cart::class => Models\Cart::class,
        Models\Contracts\CartLine::class => Models\CartLine::class,
        Models\Contracts\Country::class => Models\Country::class,
        Models\Contracts\CountryTaxClass::class => Models\CountryTaxClass::class,
        Models\Contracts\Currency::class => Models\Currency::class,
        Models\Contracts\Discount::class => Models\Discount::class,
        Models\Contracts\Inventory::class => Models\Inventory::class,
        Models\Contracts\Order::class => Models\Order::class,
        Models\Contracts\OrderLine::class => Models\OrderLine::class,
        Models\Contracts\Product::class => Models\Product::class,
        Models\Contracts\ProductCategory::class => Models\ProductCategory::class,
        Models\Contracts\ProductCustomField::class => Models\ProductCustomField::class,
        Models\Contracts\ProductItem::class => Models\ProductItem::class,
        Models\Contracts\ProductType::class => Models\ProductType::class,
        Models\Contracts\ProductVariant::class => Models\ProductVariant::class,
        Models\Contracts\ProductVariantOption::class => Models\ProductVariantOption::class,
        Models\Contracts\ShippingStatus::class => Models\ShippingStatus::class,
        Models\Contracts\TaxClass::class => Models\TaxClass::class,
        Models\Contracts\User::class => Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Specify the validation classes with the rules to use when storing and updating models
    |
    */
    'validations' => [
        Validations\Contracts\AddressValidationRules::class => Validations\Address::class,
        // Validations\Contracts\BrandValidationRules::class => Validations\Brand::class,
        Validations\Contracts\CartValidationRules::class => Validations\Cart::class,
        Validations\Contracts\CartLineValidationRules::class => Validations\CartLine::class,
        // Validations\Contracts\CountryValidationRules::class => Validations\Country::class,
        // Validations\Contracts\CountryTaxClassValidationRules::class => Validations\CountryTaxClass::class,
        // Validations\Contracts\CurrencyValidationRules::class => Validations\Currency::class,
        // Validations\Contracts\DiscountValidationRules::class => Validations\Discount::class,
        // Validations\Contracts\InventoryValidationRules::class => Validations\Inventory::class,
        Validations\Contracts\OrderValidationRules::class => Validations\Order::class,
        // Validations\Contracts\OrderLineValidationRules::class => Validations\OrderLine::class,
        // Validations\Contracts\ProductValidationRules::class => Validations\Product::class,
        // Validations\Contracts\ProductCategoryValidationRules::class => Validations\ProductCategory::class,
        // Validations\Contracts\ProductCustomFieldValidationRules::class => Validations\ProductCustomField::class,
        // Validations\Contracts\ProductItemValidationRules::class => Validations\ProductItem::class,
        // Validations\Contracts\ProductTypeValidationRules::class => Validations\ProductType::class,
        // Validations\Contracts\ProductVariantValidationRules::class => Validations\ProductVariant::class,
        // Validations\Contracts\ProductVariantOptionValidationRules::class => Validations\ProductVariantOption::class,
        // Validations\Contracts\ShippingStatusValidationRules::class => Validations\ShippingStatus::class,
        // Validations\Contracts\TaxClassValidationRules::class => Validations\TaxClass::class,
        // Validations\Contracts\UserValidationRules::class => Validations\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Addresses
    |--------------------------------------------------------------------------
    |
    */
    'addresses' => [
        'type_enum' => Enums\AddressType::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Carts
    |--------------------------------------------------------------------------
    |
    | Pipeline tasks are executed in the order they are defined
    |
    */
    'carts' => [
        'pipelines' => [
            'creation' => [
                'pipes' => [
                    Cart\Pipes\FillUserDetails::class,
                    Cart\Pipes\GenerateIdentifier::class,
                    Cart\Pipes\CalculateLines::class,
                    Cart\Pipes\CalculateTotals::class,
                ],
            ],
            'update' => [
                'pipes' => [
                    Cart\Pipes\FillUserDetails::class,
                    Cart\Pipes\UpdateLines::class,
                    Cart\Pipes\CalculateTotals::class,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Car Lines
    |--------------------------------------------------------------------------
    |
    | Pipeline tasks are executed in the order they are defined
    |
    */
    'cart_lines' => [
        'pipelines' => [
            'creation' => [
                'pipes' => [
                    CartLine\Pipes\FillProductInformations::class,
                    CartLine\Pipes\ApplyDiscount::class,
                    CartLine\Pipes\CalculateTaxes::class,
                    CartLine\Pipes\CalculateTotal::class,
                ],
            ],
            'update' => [
                'pipes' => [
                    CartLine\Pipes\FillProductInformations::class,
                    CartLine\Pipes\ApplyDiscount::class,
                    CartLine\Pipes\CalculateTaxes::class,
                    CartLine\Pipes\CalculateTotal::class,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    |
    | Pipeline tasks are executed in the order they are defined
    |
    */
    'orders' => [
        'status_enum' => Enums\OrderStatus::class,
        'pipelines' => [
            'creation' => [
                'pipes' => [
                    Order\Pipes\FillOrderFromCart::class,
                    Order\Pipes\GenerateIdentifier::class,
                    Order\Pipes\CalculateLines::class,
                    Order\Pipes\ApproveOrder::class,
                ],
            ],
        ],
        // 'statuses' => [
        //     'pending' => 'pending',
        //     'approved' => 'approved',
        //     'shipped' => 'shipped',
        //     'delivered' => 'delivered',
        //     'cancelled' => 'cancelled',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    |
    */
    'products' => [
        'status_enum' => Enums\ProductStatus::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing
    |--------------------------------------------------------------------------
    |
    | Specify the pricing formatter
    |
    */
    // 'pricing' => [
    //     'formatter' => DefaultPriceFormatter::class,
    // ],

    /*
    |--------------------------------------------------------------------------
    | Decimal
    |--------------------------------------------------------------------------
    |
    | Specify the decimal formatter
    |
    */
    // 'decimal' => [
    //     'formatter' => DefaultDecimalFormatter::class,
    // ],

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
        'routes_to_exclude' => [
            'filament.*',
            'livewire.update',
            // '*', // exclude all routes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Api routes
    |--------------------------------------------------------------------------
    |
    | Api routes configuration
    |
    */
    'routes' => [
        'api' => [
            'v1' => [
                'prefix' => 'venditio/api/v1',
                'name' => 'venditio.api.v1',
                'middleware' => ['api'],
                // 'rate_limit' => [
                //     'configure' => fn () => VenditioCore::configureRateLimiting('venditio/api/v1'),
                // ],
                'pagination' => [
                    'per_page' => 15,
                ],
            ],
            'enable' => true, // enable api routes
            'include_timestamps' => false, // include updated_at and deleted_at timestamps in api responses
            'json_resource_enable_wrapping' => false, // Illuminate\Http\Resources\Json\JsonResource::withoutWrapping();
        ],
    ],
];
