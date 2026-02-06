<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductType;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariant;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariantOption;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;
use PictaStudio\VenditioCore\Packages\Simple\Models\TaxClass;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('generates product variants from variant options', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $productType = ProductType::factory()->create();

    $colorVariant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
        'name' => 'Color',
    ]);
    $sizeVariant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
        'name' => 'Size',
    ]);

    $red = ProductVariantOption::factory()->create([
        'product_variant_id' => $colorVariant->getKey(),
        'value' => 'red',
    ]);
    $blue = ProductVariantOption::factory()->create([
        'product_variant_id' => $colorVariant->getKey(),
        'value' => 'blue',
    ]);
    $small = ProductVariantOption::factory()->create([
        'product_variant_id' => $sizeVariant->getKey(),
        'value' => 's',
    ]);
    $large = ProductVariantOption::factory()->create([
        'product_variant_id' => $sizeVariant->getKey(),
        'value' => 'l',
    ]);

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    $response = postJson("/venditio/api/v1/products/{$product->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $colorVariant->getKey(),
                'option_ids' => [$red->getKey(), $blue->getKey()],
            ],
            [
                'variant_id' => $sizeVariant->getKey(),
                'option_ids' => [$small->getKey(), $large->getKey()],
            ],
        ],
    ])->assertCreated();

    expect($response->json('meta.created'))->toBe(4);
    expect(Product::query()->where('parent_id', $product->getKey())->count())->toBe(4);

    $variant = Product::query()->where('parent_id', $product->getKey())->first();
    expect($variant->variantOptions()->count())->toBe(2);
});

it('skips existing variant combinations', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $productType = ProductType::factory()->create();

    $colorVariant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
        'name' => 'Color',
    ]);
    $red = ProductVariantOption::factory()->create([
        'product_variant_id' => $colorVariant->getKey(),
        'value' => 'red',
    ]);
    $blue = ProductVariantOption::factory()->create([
        'product_variant_id' => $colorVariant->getKey(),
        'value' => 'blue',
    ]);

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    postJson("/venditio/api/v1/products/{$product->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $colorVariant->getKey(),
                'option_ids' => [$red->getKey(), $blue->getKey()],
            ],
        ],
    ])->assertCreated();

    $response = postJson("/venditio/api/v1/products/{$product->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $colorVariant->getKey(),
                'option_ids' => [$red->getKey(), $blue->getKey()],
            ],
        ],
    ])->assertCreated();

    expect($response->json('meta.created'))->toBe(0);
    expect($response->json('meta.skipped'))->toBe(2);
});

it('rejects variant generation when product has no type', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();

    $productType = ProductType::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
    ]);
    $option = ProductVariantOption::factory()->create([
        'product_variant_id' => $variant->getKey(),
    ]);

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => null,
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    postJson("/venditio/api/v1/products/{$product->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $variant->getKey(),
                'option_ids' => [$option->getKey()],
            ],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['product_type_id']);
});

it('rejects variant generation for a variant product', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $productType = ProductType::factory()->create();

    $variant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
    ]);
    $option = ProductVariantOption::factory()->create([
        'product_variant_id' => $variant->getKey(),
    ]);

    $baseProduct = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    $variantProduct = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'parent_id' => $baseProduct->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    postJson("/venditio/api/v1/products/{$variantProduct->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $variant->getKey(),
                'option_ids' => [$option->getKey()],
            ],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['product']);
});

it('rejects variants that do not belong to the product type', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $productType = ProductType::factory()->create();
    $otherType = ProductType::factory()->create();

    $variant = ProductVariant::factory()->create([
        'product_type_id' => $otherType->getKey(),
    ]);
    $option = ProductVariantOption::factory()->create([
        'product_variant_id' => $variant->getKey(),
    ]);

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    postJson("/venditio/api/v1/products/{$product->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $variant->getKey(),
                'option_ids' => [$option->getKey()],
            ],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['variants']);
});

it('rejects options that do not belong to their variant', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $productType = ProductType::factory()->create();

    $variant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
    ]);
    $otherVariant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
    ]);
    $option = ProductVariantOption::factory()->create([
        'product_variant_id' => $otherVariant->getKey(),
    ]);

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    postJson("/venditio/api/v1/products/{$product->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $variant->getKey(),
                'option_ids' => [$option->getKey()],
            ],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['variants']);
});

it('rejects duplicate variant ids in the payload', function () {
    $brand = Brand::factory()->create();
    $taxClass = TaxClass::factory()->create();
    $productType = ProductType::factory()->create();

    $variant = ProductVariant::factory()->create([
        'product_type_id' => $productType->getKey(),
    ]);
    $option = ProductVariantOption::factory()->create([
        'product_variant_id' => $variant->getKey(),
    ]);

    $product = Product::factory()->create([
        'brand_id' => $brand->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'product_type_id' => $productType->getKey(),
        'active' => true,
        'visible_from' => null,
        'visible_until' => null,
    ]);

    postJson("/venditio/api/v1/products/{$product->getKey()}/variants", [
        'variants' => [
            [
                'variant_id' => $variant->getKey(),
                'option_ids' => [$option->getKey()],
            ],
            [
                'variant_id' => $variant->getKey(),
                'option_ids' => [$option->getKey()],
            ],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['variants']);
});
