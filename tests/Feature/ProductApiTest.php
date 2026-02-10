<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Enums\ProductStatus;
use PictaStudio\VenditioCore\Models\{Brand, Inventory, Product, ProductCategory, ProductType, ProductVariant, ProductVariantOption, TaxClass};

use function Pest\Laravel\{assertDatabaseHas, assertDatabaseMissing, getJson, patchJson, postJson};

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

it('creates a product with qty_for_unit', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();

    $response = postJson(config('venditio-core.routes.api.v1.prefix') . '/products', [
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'name' => 'Product with unit qty',
        'status' => ProductStatus::Published->value,
        'qty_for_unit' => 6,
    ])->assertCreated()
        ->assertJsonFragment([
            'name' => 'Product with unit qty',
            'qty_for_unit' => 6,
        ]);

    $productId = $response->json('id');
    assertDatabaseHas('products', ['id' => $productId, 'qty_for_unit' => 6]);
});

it('updates a product qty_for_unit', function () {
    $product = Product::factory()->create([
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
        'qty_for_unit' => null,
    ]);

    patchJson(config('venditio-core.routes.api.v1.prefix') . "/products/{$product->getKey()}", [
        'qty_for_unit' => 12,
    ])->assertOk()
        ->assertJsonFragment(['qty_for_unit' => 12]);

    assertDatabaseHas('products', ['id' => $product->getKey(), 'qty_for_unit' => 12]);
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

it('includes variants and variants options table when requested', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $productType = ProductType::factory()->create();

    $size = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
        'name' => 'Size',
        'sort_order' => 10,
    ]);
    $color = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
        'name' => 'Color',
        'sort_order' => 20,
    ]);

    $small = ProductVariantOption::factory()->create([
        'product_variant_id' => $size->getKey(),
        'name' => 's',
        'sort_order' => 10,
    ]);
    $medium = ProductVariantOption::factory()->create([
        'product_variant_id' => $size->getKey(),
        'name' => 'm',
        'sort_order' => 20,
    ]);
    $red = ProductVariantOption::factory()->create([
        'product_variant_id' => $color->getKey(),
        'name' => 'red',
        'sort_order' => 10,
    ]);
    $blue = ProductVariantOption::factory()->create([
        'product_variant_id' => $color->getKey(),
        'name' => 'blue',
        'sort_order' => 20,
    ]);

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    $variantA = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'parent_id' => $product->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);
    $variantA->variantOptions()->sync([$small->getKey(), $red->getKey()]);

    $variantB = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'parent_id' => $product->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);
    $variantB->variantOptions()->sync([$medium->getKey(), $blue->getKey()]);

    getJson(config('venditio-core.routes.api.v1.prefix') . "/products/{$product->getKey()}?include=variants,variants_options_table")
        ->assertOk()
        ->assertJsonCount(2, 'variants')
        ->assertJsonPath('variants_options_table.0.id', $size->getKey())
        ->assertJsonPath('variants_options_table.0.name', 'Size')
        ->assertJsonPath('variants_options_table.0.values.0.value', 's')
        ->assertJsonPath('variants_options_table.0.values.1.value', 'm')
        ->assertJsonPath('variants_options_table.1.id', $color->getKey())
        ->assertJsonPath('variants_options_table.1.name', 'Color')
        ->assertJsonPath('variants_options_table.1.values.0.value', 'red')
        ->assertJsonPath('variants_options_table.1.values.1.value', 'blue');
});

it('rejects unknown includes on products api', function () {
    $product = Product::factory()->create([
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    getJson(config('venditio-core.routes.api.v1.prefix') . "/products/{$product->getKey()}?include=unknown")
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['include.0']);
});
