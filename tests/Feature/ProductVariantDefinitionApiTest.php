<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Models\ProductType;
use PictaStudio\VenditioCore\Models\ProductVariant;
use PictaStudio\VenditioCore\Models\ProductVariantOption;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('creates product types, variants, and options', function () {
    $typeResponse = postJson(config('venditio-core.routes.api.v1.prefix') . '/product_types', [
        'name' => 'Apparel',
        'active' => true,
    ])->assertCreated();

    $productTypeId = $typeResponse->json('id');

    $variantResponse = postJson(config('venditio-core.routes.api.v1.prefix') . '/product_variants', [
        'product_type_id' => $productTypeId,
        'name' => 'Color',
        'sort_order' => 1,
    ])->assertCreated();

    $variantId = $variantResponse->json('id');

    postJson(config('venditio-core.routes.api.v1.prefix') . '/product_variant_options', [
        'product_variant_id' => $variantId,
        'value' => 'red',
        'sort_order' => 1,
    ])->assertCreated()
        ->assertJsonFragment([
            'product_variant_id' => $variantId,
            'value' => 'red',
        ]);
});

it('filters variants by product type', function () {
    $productType = ProductType::factory()->create();
    $otherType = ProductType::factory()->create();

    ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
    ]);
    ProductVariant::factory()->create([
        'product_type_id' => $otherType->getKey(),
    ]);

    $response = getJson(config('venditio-core.routes.api.v1.prefix') . '/product_variants?product_type_id=' . $productType->getKey())
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1);
});

it('filters variant options by variant', function () {
    $variant = ProductVariant::factory()->create();
    $otherVariant = ProductVariant::factory()->create();

    ProductVariantOption::factory()->create([
        'product_variant_id' => $variant->getKey(),
        'value' => 'red',
    ]);
    ProductVariantOption::factory()->create([
        'product_variant_id' => $otherVariant->getKey(),
        'value' => 'blue',
    ]);

    $response = getJson(config('venditio-core.routes.api.v1.prefix') . '/product_variant_options?product_variant_id=' . $variant->getKey())
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1);
});
