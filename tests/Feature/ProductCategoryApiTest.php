<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;

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

    $response = postJson('/venditio/api/v1/product_categories', $payload)
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

    patchJson("/venditio/api/v1/product_categories/{$category->id}", [
        'name' => 'New Name',
        'sort_order' => 2,
    ])->assertOk()
        ->assertJsonFragment([
            'name' => 'New Name',
            'sort_order' => 2,
        ]);

    assertDatabaseHas('product_categories', [
        'id' => $category->id,
        'name' => 'New Name',
        'sort_order' => 2,
    ]);
});
