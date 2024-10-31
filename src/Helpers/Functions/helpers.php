<?php

namespace PictaStudio\VenditioCore\Helpers\Functions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Managers\Contracts\AuthManager as AuthManagerContract;
use PictaStudio\VenditioCore\Packages\Simple\Models\User;

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
     * Tries to resolve the model for the configured package type: simple or advanced,
     * if the model is not found for the configured package type, it will try to resolve it from the simple package
     *
     * @param string $model Can be one of the following values:
     *                      'address', 'brand', 'cart', 'cart_line', 'country', 
     *                      'country_tax_class', 'currency', 'discount', 'inventory', 
     *                      'order', 'order_line', 'product', 'product_category', 
     *                      'shipping_status', 'tax_class', 'user'.
     *                     'product_custom_field', 'product_item', 'product_type',
     *                      'product_variant', 'product_variant_option'
     */
    function resolve_model(string $model): string
    {
        $packageType = VenditioCore::getPackageType();

        return config(
            'venditio-core.models.' . $packageType->value . '.' . $model,
            config('venditio-core.models.simple.' . $model)
        );
    }
}

if (!function_exists('query')) {
    /**
     * Initialize a query builder for the given model
     * 
     * @param string $model Can be one of the following values:
     *                      'address', 'brand', 'cart', 'cart_line', 'country', 
     *                      'country_tax_class', 'currency', 'discount', 'inventory', 
     *                      'order', 'order_line', 'product', 'product_category', 
     *                      'shipping_status', 'tax_class', 'user'.
     *                     'product_custom_field', 'product_item', 'product_type',
     *                      'product_variant', 'product_variant_option'
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
     * @param string $model Can be one of the following values:
     *                      'address', 'brand', 'cart', 'cart_line', 'country', 
     *                      'country_tax_class', 'currency', 'discount', 'inventory', 
     *                      'order', 'order_line', 'product', 'product_category', 
     *                      'shipping_status', 'tax_class', 'user'.
     *                     'product_custom_field', 'product_item', 'product_type',
     *                      'product_variant', 'product_variant_option'
     */
    function get_fresh_model_instance(string $model): Model
    {
        return (new (resolve_model($model)))->updateTimestamps();
    }
}

if (!function_exists('resolve_purchasable_product_model')) {
    /**
     * Resolve the product model for the current package
     * 
     * - if simple package is used -> PictaStudio\VenditioCore\Packages\Simple\Models\Product
     * - if advanced package is used -> PictaStudio\VenditioCore\Packages\Advanced\Models\ProductItem
     */
    function resolve_purchasable_product_model(): string
    {
        if (VenditioCore::isSimple()) {
            return resolve_model('product');
        }
    
        return resolve_model('product_item');
    }
}

if (!function_exists('query_purchasable_product_model')) {
    function query_purchasable_product_model(): Builder
    {
        return resolve_purchasable_product_model()::query();
    }
}

if (!function_exists('resolve_dto')) {
    /**
     * Resolve the DTO class for the given model/type, returns the fully qualified class name
     * 
     * @param string $dto Can be one of the following values:
     *                     'order', 'cart', 'cart_line', 'address'
     */
    function resolve_dto(string $dto): string
    {
        // $packageType = VenditioCore::getPackageType();
    
        return match ($dto) {
            'order' => config('venditio-core.orders.dto'),
            'cart' => config('venditio-core.carts.dto'),
            'cart_line' => config('venditio-core.cart_lines.dto'),
            'address' => config('venditio-core.addresses.dto'),
        };
    }
}
