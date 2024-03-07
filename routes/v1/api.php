<?php

use Illuminate\Support\Facades\Route;
use PictaStudio\VenditioCore\Http\Controllers\Api\V1\ProductCategoryController;
use PictaStudio\VenditioCore\Http\Controllers\Api\V1\ProductItemController;

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

Route::apiResource('product_items', ProductItemController::class)->only(['index', 'show']);
Route::apiResource('product_categories', ProductCategoryController::class)->only(['index', 'show']);
