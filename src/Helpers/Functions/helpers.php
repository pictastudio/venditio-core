<?php

namespace PictaStudio\VenditioCore\Helpers\Functions;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\VenditioCore\Managers\Contracts\AuthManager as AuthManagerContract;
use PictaStudio\VenditioCore\Models\User;

if (!function_exists('auth_manager')) {
    /**
     * Get the auth manager instance
     */
    function auth_manager(User|Authenticatable|null $user = null): AuthManagerContract
    {
        return app(AuthManagerContract::class, ['user' => $user]);
    }
}

if (!function_exists('resolve_model')) {
    /**
     * Resolve the configured model class
     *
     * @param  string  $model  Can be one of the following values:
     *                         'address', 'brand', 'cart', 'cart_line', 'country',
     *                         'country_tax_class', 'currency', 'discount', 'discount_application', 'inventory',
     *                         'order', 'order_line', 'product', 'product_category',
     *                         'shipping_status', 'tax_class', 'user'.
     *                         'product_custom_field', 'product_type',
     *                         'product_variant', 'product_variant_option',
     *                         'price_list', 'price_list_price'
     */
    function resolve_model(string $model): string
    {
        return config('venditio-core.models.' . $model);
    }
}

if (!function_exists('query')) {
    /**
     * Initialize a query builder for the given model
     *
     * @param  string  $model  Can be one of the following values:
     *                         'address', 'brand', 'cart', 'cart_line', 'country',
     *                         'country_tax_class', 'currency', 'discount', 'discount_application', 'inventory',
     *                         'order', 'order_line', 'product', 'product_category',
     *                         'shipping_status', 'tax_class', 'user'.
     *                         'product_custom_field', 'product_type',
     *                         'product_variant', 'product_variant_option',
     *                         'price_list', 'price_list_price'
     */
    function query(string $model): Builder
    {
        return resolve_model($model)::query();
    }
}

if (!function_exists('get_fresh_model_instance')) {
    /**
     * Get a fresh instance of the given model
     *
     * @param  string  $model  Can be one of the following values:
     *                         'address', 'brand', 'cart', 'cart_line', 'country',
     *                         'country_tax_class', 'currency', 'discount', 'discount_application', 'inventory',
     *                         'order', 'order_line', 'product', 'product_category',
     *                         'shipping_status', 'tax_class', 'user'.
     *                         'product_custom_field', 'product_type',
     *                         'product_variant', 'product_variant_option',
     *                         'price_list', 'price_list_price'
     */
    function get_fresh_model_instance(string $model): Model
    {
        return (new (resolve_model($model)))->updateTimestamps();
    }
}

if (!function_exists('resolve_dto')) {
    /**
     * Resolve the DTO class for the given model/type, returns the fully qualified class name
     *
     * @param  string  $dto  Can be one of the following values:
     *                       'order', 'cart', 'cart_line', 'address'
     * @return class-string
     */
    function resolve_dto(string $dto): string
    {
        return match ($dto) {
            'order' => config('venditio-core.order.dto'),
            'cart' => config('venditio-core.cart.dto'),
            'cart_line' => config('venditio-core.cart_line.dto'),
            'address' => config('venditio-core.addresses.dto'),
        };
    }
}

if (!function_exists('resolve_enum')) {
    /**
     * Resolve the enum class for the given model/type, returns the fully qualified class name
     *
     * @param  string  $enum  Can be one of the following values:
     *                        'order_status', 'cart_status', 'cart_line_status', 'address_type'
     * @return class-string
     */
    function resolve_enum(string $enum): string
    {
        return match ($enum) {
            'order_status' => config('venditio-core.order.status_enum'),
            'cart_status' => config('venditio-core.cart.status_enum'),
            'cart_line_status' => config('venditio-core.cart_line.status_enum'),
            'address_type' => config('venditio-core.addresses.type_enum'),
        };
    }
}
