<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Enums\ProductStatus;
use PictaStudio\VenditioCore\Models\{Brand, Inventory, Product, ProductCategory, TaxClass};

use function Pest\Laravel\{assertDatabaseHas, assertDatabaseMissing, patchJson, postJson};

uses(RefreshDatabase::class);

it('creates a product with categories', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $category = ProductCategory::factory()->create();

    $payload = [
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'name' => 'Sample Product',
        'status' => ProductStatus::Published->value,
        'category_ids' => [$category->getKey()],
    ];

    $response = postJson(config('venditio-core.routes.api.v1.prefix') . '/products', $payload)
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'Sample Product',
            'status' => ProductStatus::Published->value,
        ]);

    $productId = $response->json('id');

    expect($productId)->not->toBeNull();
    assertDatabaseHas('products', ['id' => $productId, 'name' => 'Sample Product']);
    assertDatabaseHas('product_category_product', [
        'product_id' => $productId,
        'product_category_id' => $category->getKey(),
    ]);
});

it('validates product creation', function () {
    postJson(config('venditio-core.routes.api.v1.prefix') . '/products', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['tax_class_id', 'name', 'status']);
});

it('updates product categories when provided', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $category = ProductCategory::factory()->create();
    $otherCategory = ProductCategory::factory()->create();

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    $product->categories()->sync([$category->getKey()]);

    patchJson(config('venditio-core.routes.api.v1.prefix') . "/products/{$product->getKey()}", [
        'category_ids' => [$otherCategory->getKey()],
    ])->assertOk();

    assertDatabaseMissing('product_category_product', [
        'product_id' => $product->getKey(),
        'product_category_id' => $category->getKey(),
    ]);
    assertDatabaseHas('product_category_product', [
        'product_id' => $product->getKey(),
        'product_category_id' => $otherCategory->getKey(),
    ]);
});

it('creates a product with nested inventory fields', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();

    $response = postJson(config('venditio-core.routes.api.v1.prefix') . '/products', [
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'name' => 'Inventory Product',
        'status' => ProductStatus::Published->value,
        'inventory' => [
            'stock' => 120,
            'stock_reserved' => 15,
            'stock_min' => 10,
            'price' => 99.50,
            'price_includes_tax' => true,
            'purchase_price' => 65.10,
        ],
    ])->assertCreated();

    $productId = $response->json('id');

    assertDatabaseHas('inventories', [
        'product_id' => $productId,
        'stock' => 120,
        'stock_reserved' => 15,
        'stock_min' => 10,
        'price' => 99.50,
        'price_includes_tax' => true,
        'purchase_price' => 65.10,
        'stock_available' => 105,
    ]);
});

it('updates nested inventory fields via product api', function () {
    $product = Product::factory()->create([
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    Inventory::factory()->create([
        'product_id' => $product->getKey(),
        'stock' => 10,
        'stock_reserved' => 2,
        'price' => 30,
    ]);

    patchJson(config('venditio-core.routes.api.v1.prefix') . "/products/{$product->getKey()}", [
        'inventory' => [
            'stock' => 75,
            'stock_reserved' => 5,
            'stock_min' => 8,
            'price' => 120.00,
            'purchase_price' => 70.00,
        ],
    ])->assertOk();

    assertDatabaseHas('inventories', [
        'product_id' => $product->getKey(),
        'stock' => 75,
        'stock_reserved' => 5,
        'stock_min' => 8,
        'price' => 120.00,
        'purchase_price' => 70.00,
        'stock_available' => 70,
    ]);
});
