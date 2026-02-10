<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Models\{Brand, Order};

use function Pest\Laravel\{deleteJson, getJson, postJson};

uses(RefreshDatabase::class);

it('registers index endpoints for all exposed models', function () {
    $prefix = config('venditio-core.routes.api.v1.prefix');

    $endpoints = [
        '/products',
        '/product_categories',
        '/product_types',
        '/product_variants',
        '/product_variant_options',
        '/carts',
        '/orders',
        '/addresses',
        '/brands',
        '/inventories',
        '/countries',
        '/country_tax_classes',
        '/currencies',
        '/tax_classes',
        '/shipping_statuses',
        '/discounts',
        '/discount_applications',
        '/product_custom_fields',
        '/cart_lines',
        '/order_lines',
    ];

    foreach ($endpoints as $endpoint) {
        getJson($prefix . $endpoint)
            ->assertStatus(200);
    }
});

it('supports deleting brands and orders through api resources', function () {
    $prefix = config('venditio-core.routes.api.v1.prefix');

    $brand = Brand::factory()->create();
    deleteJson($prefix . '/brands/' . $brand->getKey())->assertNoContent();

    $order = Order::factory()->create();
    deleteJson($prefix . '/orders/' . $order->getKey())->assertNoContent();
});

it('supports creating brands through api resources', function () {
    $prefix = config('venditio-core.routes.api.v1.prefix');

    postJson($prefix . '/brands', ['name' => 'Acme'])
        ->assertStatus(201)
        ->assertJsonFragment(['name' => 'Acme']);
});
