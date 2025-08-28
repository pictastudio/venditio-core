<?php

use Illuminate\Support\Facades\Route;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Http\Controllers\Api\V1\{AddressController, BrandController, CartController, OrderController, ProductCategoryController, ProductController};
use PictaStudio\VenditioCore\Packages\Advanced\Http\Controllers\Api\V1\ProductItemController;

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

Route::apiResource('products', ProductController::class)->only(['index', 'show']);

if (VenditioCore::isAdvanced()) {
    Route::apiResource('product_items', ProductItemController::class)->only(['index', 'show']);
}

Route::apiResource('product_categories', ProductCategoryController::class)->only(['index', 'show']);
Route::apiResource('carts', CartController::class);
Route::post('carts/{cart}/add_lines', [CartController::class, 'addLines']);
Route::patch('carts/{cart}/update_lines', [CartController::class, 'updateLines']);
Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store', 'update']);
Route::apiResource('addresses', AddressController::class)->only(['index', 'show', 'store', 'update']);
Route::apiResource('brands', BrandController::class)->only(['index', 'show', 'store', 'update']);
