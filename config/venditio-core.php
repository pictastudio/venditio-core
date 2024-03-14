<?php

use PictaStudio\VenditioCore\Enums\OrderStatus;
use PictaStudio\VenditioCore\Enums\ProductStatus;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Formatters\Decimal\DefaultDecimalFormatter;
use PictaStudio\VenditioCore\Formatters\Pricing\DefaultPriceFormatter;
use PictaStudio\VenditioCore\Managers\AuthManager;
use PictaStudio\VenditioCore\Pipelines\Cart;
use PictaStudio\VenditioCore\Pipelines\Order;

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
        'roles' => [
            'root' => AuthManager::ROLE_ROOT,
            'admin' => AuthManager::ROLE_ADMIN,
            'user' => AuthManager::ROLE_USER,
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
                    Cart\Pipes\CalculateLines::class,
                    Cart\Pipes\GenerateIdentifier::class,
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
    | Orders
    |--------------------------------------------------------------------------
    |
    | Pipeline tasks are executed in the order they are defined
    |
    */
    'orders' => [
        'status_enum' => OrderStatus::class,
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
        'status_enum' => ProductStatus::class,
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
