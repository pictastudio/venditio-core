<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\Venditio\Models\{ProductType, TaxClass};

use function Pest\Laravel\{patchJson, postJson};

uses(RefreshDatabase::class);

describe('product type is_default', function () {
    it('allows creating a product type with is_default true when no default exists', function () {
        $response = postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
            'name' => 'Default',
            'active' => true,
            'is_default' => true,
        ])->assertCreated()
            ->assertJsonFragment(['name' => 'Default', 'is_default' => true]);

        expect($response->json('id'))->not->toBeNull();
    });

    it('rejects creating a product type with is_default true when another is already default', function () {
        ProductType::factory()->create([
            'name' => 'Existing Default',
            'is_default' => true,
        ]);

        postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
            'name' => 'Second Default',
            'active' => true,
            'is_default' => true,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['is_default']);
    });

    it('allows creating a product type with is_default false when a default exists', function () {
        ProductType::factory()->create(['is_default' => true]);

        postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
            'name' => 'Non-default type',
            'active' => true,
            'is_default' => false,
        ])->assertCreated()
            ->assertJsonFragment(['is_default' => false]);
    });

    it('rejects updating a product type to is_default true when another is already default', function () {
        ProductType::factory()->create(['name' => 'Default', 'is_default' => true, 'active' => true]);
        $other = ProductType::factory()->create(['name' => 'Other', 'is_default' => false, 'active' => true]);

        patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$other->getKey()}", [
            'is_default' => true,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['is_default']);
    });

    it('allows updating a product type to is_default true when no other is default', function () {
        $productType = ProductType::factory()->create(['name' => 'Only one', 'is_default' => false, 'active' => true]);

        patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productType->getKey()}", [
            'is_default' => true,
        ])->assertOk()
            ->assertJsonFragment(['is_default' => true]);
    });

    it('allows updating the current default to is_default true (no change)', function () {
        $productType = ProductType::factory()->create(['name' => 'Default', 'is_default' => true, 'active' => true]);

        patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productType->getKey()}", [
            'name' => 'Default (renamed)',
            'is_default' => true,
        ])->assertOk();
    });
});

describe('tax class is_default', function () {
    it('allows creating a tax class with is_default true when no default exists', function () {
        $response = postJson(config('venditio.routes.api.v1.prefix') . '/tax_classes', [
            'name' => 'Standard',
            'is_default' => true,
        ])->assertCreated()
            ->assertJsonFragment(['name' => 'Standard', 'is_default' => true]);

        expect($response->json('id'))->not->toBeNull();
    });

    it('rejects creating a tax class with is_default true when another is already default', function () {
        TaxClass::factory()->create(['name' => 'Standard', 'is_default' => true]);

        postJson(config('venditio.routes.api.v1.prefix') . '/tax_classes', [
            'name' => 'Reduced',
            'is_default' => true,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['is_default']);
    });

    it('allows creating a tax class with is_default false when a default exists', function () {
        TaxClass::factory()->create(['is_default' => true]);

        postJson(config('venditio.routes.api.v1.prefix') . '/tax_classes', [
            'name' => 'Reduced',
            'is_default' => false,
        ])->assertCreated()
            ->assertJsonFragment(['is_default' => false]);
    });

    it('rejects updating a tax class to is_default true when another is already default', function () {
        TaxClass::factory()->create(['name' => 'Standard', 'is_default' => true]);
        $other = TaxClass::factory()->create(['name' => 'Reduced', 'is_default' => false]);

        patchJson(config('venditio.routes.api.v1.prefix') . "/tax_classes/{$other->getKey()}", [
            'is_default' => true,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['is_default']);
    });

    it('allows updating a tax class to is_default true when no other is default', function () {
        $taxClass = TaxClass::factory()->create(['name' => 'Only one', 'is_default' => false]);

        patchJson(config('venditio.routes.api.v1.prefix') . "/tax_classes/{$taxClass->getKey()}", [
            'is_default' => true,
        ])->assertOk()
            ->assertJsonFragment(['is_default' => true]);
    });

    it('allows updating the current default tax class to is_default true (no change)', function () {
        $taxClass = TaxClass::factory()->create(['name' => 'Standard', 'is_default' => true]);

        patchJson(config('venditio.routes.api.v1.prefix') . "/tax_classes/{$taxClass->getKey()}", [
            'name' => 'Standard VAT',
            'is_default' => true,
        ])->assertOk();
    });
});
