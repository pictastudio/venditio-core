<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\Venditio\Models\ProductCategory;

use function Pest\Laravel\{assertDatabaseHas, getJson, patchJson, postJson};

uses(RefreshDatabase::class);

it('creates a product category', function () {
    $payload = [
        'name' => 'Shoes',
        'active' => true,
        'sort_order' => 1,
    ];

    $response = postJson(config('venditio.routes.api.v1.prefix') . '/product_categories', $payload)
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'Shoes',
            'active' => true,
            'sort_order' => 1,
        ]);

    $categoryId = $response->json('id');

    expect($categoryId)->not->toBeNull();
    assertDatabaseHas('product_categories', ['id' => $categoryId]);
    assertDatabaseHas('translations', [
        'translatable_type' => (new ProductCategory)->getMorphClass(),
        'translatable_id' => $categoryId,
        'locale' => app()->getLocale(),
        'attribute' => 'name',
        'value' => 'Shoes',
    ]);
});

it('updates a product category', function () {
    $category = ProductCategory::factory()->create([
        'name' => 'Old Name',
        'sort_order' => 1,
    ]);

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_categories/{$category->getKey()}", [
        'name' => 'New Name',
        'sort_order' => 2,
    ])->assertOk()
        ->assertJsonFragment([
            'name' => 'New Name',
            'sort_order' => 2,
        ]);

    assertDatabaseHas('product_categories', [
        'id' => $category->getKey(),
        'sort_order' => 2,
    ]);
    assertDatabaseHas('translations', [
        'translatable_type' => (new ProductCategory)->getMorphClass(),
        'translatable_id' => $category->getKey(),
        'locale' => app()->getLocale(),
        'attribute' => 'name',
        'value' => 'New Name',
    ]);
});

it('returns product categories as a tree when as_tree is true', function () {
    $root = ProductCategory::factory()->create([
        'name' => 'Root',
        'sort_order' => 1,
    ]);

    $child = ProductCategory::factory()->create([
        'name' => 'Child',
        'parent_id' => $root->getKey(),
        'sort_order' => 2,
    ]);

    ProductCategory::factory()->create([
        'name' => 'Other Root',
        'sort_order' => 3,
    ]);

    getJson(config('venditio.routes.api.v1.prefix') . '/product_categories?as_tree=1')
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonPath('0.name', 'Root')
        ->assertJsonPath('0.children.0.name', 'Child')
        ->assertJsonPath('1.name', 'Other Root');

    expect($child->fresh()->path)->not->toBeNull();
});
