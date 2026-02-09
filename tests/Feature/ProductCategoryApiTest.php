<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Models\ProductCategory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

it('creates a product category', function () {
    $payload = [
        'name' => 'Shoes',
        'active' => true,
        'sort_order' => 1,
    ];

    $response = postJson(config('venditio-core.routes.api.v1.prefix') . '/product_categories', $payload)
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'Shoes',
            'active' => true,
            'sort_order' => 1,
        ]);

    $categoryId = $response->json('id');

    expect($categoryId)->not->toBeNull();
    assertDatabaseHas('product_categories', ['id' => $categoryId, 'name' => 'Shoes']);
});

it('updates a product category', function () {
    $category = ProductCategory::factory()->create([
        'name' => 'Old Name',
        'sort_order' => 1,
    ]);

    patchJson(config('venditio-core.routes.api.v1.prefix') . "/product_categories/{$category->getKey()}", [
        'name' => 'New Name',
        'sort_order' => 2,
    ])->assertOk()
        ->assertJsonFragment([
            'name' => 'New Name',
            'sort_order' => 2,
        ]);

    assertDatabaseHas('product_categories', [
        'id' => $category->getKey(),
        'name' => 'New Name',
        'sort_order' => 2,
    ]);
});
