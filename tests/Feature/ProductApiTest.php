<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Packages\Simple\Enums\ProductStatus;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;
use PictaStudio\VenditioCore\Packages\Simple\Models\TaxClass;

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
        'brand_id' => $brand->id,
        'tax_class_id' => $taxClass->id,
        'name' => 'Sample Product',
        'status' => ProductStatus::Published->value,
        'category_ids' => [$category->id],
    ];

    $response = postJson('/venditio/api/v1/products', $payload)
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
        'product_category_id' => $category->id,
    ]);
});

it('validates product creation', function () {
    postJson('/venditio/api/v1/products', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['brand_id', 'tax_class_id', 'name', 'status']);
});

it('updates product categories when provided', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $category = ProductCategory::factory()->create();
    $otherCategory = ProductCategory::factory()->create();

    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'tax_class_id' => $taxClass->id,
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    $product->categories()->sync([$category->id]);

    patchJson("/venditio/api/v1/products/{$product->id}", [
        'category_ids' => [$otherCategory->id],
    ])->assertOk();

    assertDatabaseMissing('product_category_product', [
        'product_id' => $product->id,
        'product_category_id' => $category->id,
    ]);
    assertDatabaseHas('product_category_product', [
        'product_id' => $product->id,
        'product_category_id' => $otherCategory->id,
    ]);
});
