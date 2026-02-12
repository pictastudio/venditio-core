<?php

use Illuminate\Support\Facades\Route;
use PictaStudio\VenditioCore\Http\Controllers\Api\V1\{AddressController, BrandController, CartController, CartLineController, CountryController, CountryTaxClassController, CurrencyController, DiscountApplicationController, DiscountController, InventoryController, OrderController, OrderLineController, ProductCategoryController, ProductController, ProductCustomFieldController, ProductTypeController, ProductVariantController, ProductVariantOptionController, ShippingStatusController, TaxClassController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('products', ProductController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::get('products/{product}/variants', [ProductController::class, 'variants'])->name('products.variants');
Route::post('products/{product}/variants', [ProductController::class, 'createVariants'])->name('products.createVariants');

Route::apiResource('product_categories', ProductCategoryController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::apiResource('product_types', ProductTypeController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::apiResource('product_variants', ProductVariantController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::apiResource('product_variant_options', ProductVariantOptionController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::apiResource('carts', CartController::class);
Route::post('carts/{cart}/add_lines', [CartController::class, 'addLines'])->name('carts.addLines');
Route::post('carts/{cart}/remove_lines', [CartController::class, 'removeLines'])->name('carts.removeLines');
Route::post('carts/{cart}/add_discount', [CartController::class, 'addDiscount'])->name('carts.addDiscount');
Route::patch('carts/{cart}/update_lines', [CartController::class, 'updateLines'])->name('carts.updateLines');
Route::apiResource('orders', OrderController::class);
Route::apiResource('addresses', AddressController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('inventories', InventoryController::class);
Route::apiResource('countries', CountryController::class);
Route::apiResource('country_tax_classes', CountryTaxClassController::class);
Route::apiResource('currencies', CurrencyController::class);
Route::apiResource('tax_classes', TaxClassController::class);
Route::apiResource('shipping_statuses', ShippingStatusController::class);
Route::apiResource('discounts', DiscountController::class);
Route::apiResource('discount_applications', DiscountApplicationController::class);
Route::apiResource('product_custom_fields', ProductCustomFieldController::class);
Route::apiResource('cart_lines', CartLineController::class);
Route::apiResource('order_lines', OrderLineController::class);
