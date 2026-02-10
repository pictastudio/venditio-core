<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Enums\ProductStatus;
use PictaStudio\VenditioCore\Models\Brand;
use PictaStudio\VenditioCore\Models\Product;
use PictaStudio\VenditioCore\Models\ProductCategory;
use PictaStudio\VenditioCore\Models\TaxClass;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;
use function Pest\Laravel\patchJson;

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
