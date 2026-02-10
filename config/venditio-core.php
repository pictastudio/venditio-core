<?php

use PictaStudio\VenditioCore\Dto;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Managers;
use PictaStudio\VenditioCore\Enums;
use PictaStudio\VenditioCore\Discounts;
use PictaStudio\VenditioCore\Models;
use PictaStudio\VenditioCore\Pipelines\Cart;
use PictaStudio\VenditioCore\Pipelines\CartLine;
use PictaStudio\VenditioCore\Pipelines\Order;

return [

    'activity_log' => [
        'enabled' => env('VENDITIO_CORE_ACTIVITY_LOG_ENABLED', false),
        'log_name' => env('VENDITIO_CORE_ACTIVITY_LOG_NAME', 'venditio-core'),
        'log_except' => env('VENDITIO_CORE_ACTIVITY_LOG_EXCEPT', ['updated_at']),
    ],

    'policies' => [
        'register' => env('VENDITIO_CORE_POLICIES_REGISTER', true),
    ],

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
            'product-type',
            'product-variant',
            'product-variant-option',
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
    | Host applications can override any model class to extend behavior.
    |
    */
    'models' => [
        'address' => Models\Address::class,
        'brand' => Models\Brand::class,
        'cart' => Models\Cart::class,
        'cart_line' => Models\CartLine::class,
        'country' => Models\Country::class,
        'country_tax_class' => Models\CountryTaxClass::class,
        'currency' => Models\Currency::class,
        'discount' => Models\Discount::class,
        'discount_application' => Models\DiscountApplication::class,
        'inventory' => Models\Inventory::class,
        'order' => Models\Order::class,
        'order_line' => Models\OrderLine::class,
        'product' => Models\Product::class,
        'product_category' => Models\ProductCategory::class,
        'shipping_status' => Models\ShippingStatus::class,
        'tax_class' => Models\TaxClass::class,
        'user' => Models\User::class,
        'product_custom_field' => Models\ProductCustomField::class,
        'product_type' => Models\ProductType::class,
        'product_variant' => Models\ProductVariant::class,
        'product_variant_option' => Models\ProductVariantOption::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Specify the validation classes with the rules to use when storing and updating models
    |
    */
    // 'validations' => [
    //     Validations\Contracts\AddressValidationRules::class => Validations\Address::class,
    //     // Validations\Contracts\BrandValidationRules::class => Validations\Brand::class,
    //     Validations\Contracts\CartValidationRules::class => Validations\Cart::class,
    //     Validations\Contracts\CartLineValidationRules::class => Validations\CartLine::class,
    //     // Validations\Contracts\CountryValidationRules::class => Validations\Country::class,
    //     // Validations\Contracts\CountryTaxClassValidationRules::class => Validations\CountryTaxClass::class,
    //     // Validations\Contracts\CurrencyValidationRules::class => Validations\Currency::class,
    //     // Validations\Contracts\DiscountValidationRules::class => Validations\Discount::class,
    //     // Validations\Contracts\InventoryValidationRules::class => Validations\Inventory::class,
    //     Validations\Contracts\OrderValidationRules::class => Validations\Order::class,
    //     // Validations\Contracts\OrderLineValidationRules::class => Validations\OrderLine::class,
    //     // Validations\Contracts\ProductValidationRules::class => Validations\Product::class,
    //     // Validations\Contracts\ProductCategoryValidationRules::class => Validations\ProductCategory::class,
    //     // Validations\Contracts\ProductCustomFieldValidationRules::class => Validations\ProductCustomField::class,
    //     // Validations\Contracts\ProductItemValidationRules::class => Validations\ProductItem::class,
    //     // Validations\Contracts\ProductTypeValidationRules::class => Validations\ProductType::class,
    //     // Validations\Contracts\ProductVariantValidationRules::class => Validations\ProductVariant::class,
    //     // Validations\Contracts\ProductVariantOptionValidationRules::class => Validations\ProductVariantOption::class,
    //     // Validations\Contracts\ShippingStatusValidationRules::class => Validations\ShippingStatus::class,
    //     // Validations\Contracts\TaxClassValidationRules::class => Validations\TaxClass::class,
    //     // Validations\Contracts\UserValidationRules::class => Validations\User::class,
    // ],

    /*
    |--------------------------------------------------------------------------
    | Address
    |--------------------------------------------------------------------------
    |
    */
    'addresses' => [
        'type_enum' => Enums\AddressType::class,

        'dto' => Dto\AddressDto::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    |
    | Pipeline tasks are executed in the order they are defined
    |
    */
    'cart' => [
        'status_enum' => Enums\CartStatus::class,

        'dto' => Dto\CartDto::class,

        // pipelines
        'pipelines' => [
            'create' => [
                'pipes' => [
                    Cart\Pipes\FillDataFromPayload::class,
                    Cart\Pipes\GenerateIdentifier::class,
                    Cart\Pipes\CalculateLines::class,
                    Cart\Pipes\ApplyDiscounts::class,
                    Cart\Pipes\CalculateTotals::class,
                ],
            ],
            'update' => [
                'pipes' => [
                    Cart\Pipes\FillDataFromPayload::class,
                    Cart\Pipes\UpdateLines::class,
                    Cart\Pipes\ApplyDiscounts::class,
                    Cart\Pipes\CalculateTotals::class,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Line
    |--------------------------------------------------------------------------
    |
    */
    'cart_line' => [
        'dto' => Dto\CartLineDto::class,

        // pipeline tasks are executed in the order they are defined
        'pipelines' => [
            'create' => [
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
    | Order
    |--------------------------------------------------------------------------
    |
    */
    'order' => [
        'status_enum' => Enums\OrderStatus::class,

        'dto' => Dto\OrderDto::class,

        // pipeline tasks are executed in the order they are defined
        'pipelines' => [
            'create' => [
                'pipes' => [
                    Order\Pipes\FillOrderFromCart::class,
                    Order\Pipes\GenerateIdentifier::class,
                    Order\Pipes\ApplyDiscounts::class,
                    Order\Pipes\CalculateLines::class,
                    Order\Pipes\RegisterDiscountUsages::class,
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
    | Product
    |--------------------------------------------------------------------------
    |
    */
    'product' => [
        'status_enum' => Enums\ProductStatus::class,
        'measuring_unit_enum' => Enums\ProductMeasuringUnit::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Discounts
    |--------------------------------------------------------------------------
    |
    | Bindings and rule classes used to evaluate discount eligibility.
    | Host applications can override the calculator/resolver/usage recorder
    | implementations or completely replace the rules list.
    |
    */
    'discounts' => [
        'calculator' => Discounts\DiscountCalculator::class,
        'discountables_resolver' => Discounts\DiscountablesResolver::class,
        'usage_recorder' => Discounts\DiscountUsageRecorder::class,
        'rules' => [
            Discounts\Rules\LineScopeRule::class,
            Discounts\Rules\ActiveWindowRule::class,
            Discounts\Rules\MaxUsesRule::class,
            Discounts\Rules\MaxUsesPerUserRule::class,
            Discounts\Rules\OncePerCartRule::class,
        ],
        'cart_total' => [
            'calculator' => Discounts\CartTotalDiscountCalculator::class,
            // `subtotal` applies coupon to line totals (tax included),
            // `checkout_total` also includes shipping + payment fees.
            'base' => 'subtotal',
            'rules' => [
                Discounts\Rules\ActiveWindowRule::class,
                Discounts\Rules\MaxUsesRule::class,
                Discounts\Rules\MaxUsesPerUserRule::class,
                Discounts\Rules\OncePerCartRule::class,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Product Variants
    |--------------------------------------------------------------------------
    |
    */
    'product_variants' => [
        'name_separator' => ' / ',
        'name_suffix_separator' => ' - ',
        'copy_categories' => true,
        'copy_attributes_exclude' => [
            'id',
            'slug',
            'sku',
            'ean',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
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
        'routes_to_exclude' => [ // routes to exclude from applying the scopes
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
                'prefix' => 'api/venditio/v1',
                'name' => 'api.venditio.v1',
                'middleware' => [
                    'api',
                    // 'auth:sanctum',
                ],
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
