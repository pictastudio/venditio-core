<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PictaStudio\VenditioCore\Dto\{CartDto, OrderDto};
use PictaStudio\VenditioCore\Enums\{DiscountType, ProductStatus};
use PictaStudio\VenditioCore\Models\{Country, CountryTaxClass, DiscountApplication, Product, ProductCategory, TaxClass, User};
use PictaStudio\VenditioCore\Pipelines\Cart\CartCreationPipeline;
use PictaStudio\VenditioCore\Pipelines\Order\OrderCreationPipeline;

uses(RefreshDatabase::class);

beforeEach(function () {
    if (Schema::hasTable('users')) {
        return;
    }

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->timestamps();
    });
});

function setupTaxEnvironment(TaxClass $taxClass): void
{
    $country = Country::query()->create([
        'name' => 'Italy',
        'iso_2' => 'IT',
        'iso_3' => 'ITA',
        'phone_code' => '+39',
        'currency_code' => 'EUR',
        'flag_emoji' => 'it',
        'capital' => 'Rome',
        'native' => 'Italia',
    ]);

    CountryTaxClass::query()->create([
        'country_id' => $country->getKey(),
        'tax_class_id' => $taxClass->getKey(),
        'rate' => 22,
    ]);
}

function createProduct(float $price, TaxClass $taxClass, bool $priceIncludesTax = false): Product
{
    /** @var Product $product */
    $product = Product::factory()->create([
        'tax_class_id' => $taxClass->getKey(),
        'status' => ProductStatus::Published,
        'active' => true,
        'visible_from' => now()->subDay(),
        'visible_until' => now()->addDay(),
    ]);

    $product->inventory()->updateOrCreate([], [
        'stock' => 100,
        'stock_reserved' => 0,
        'stock_available' => 100,
        'stock_min' => 0,
        'price' => $price,
        'price_includes_tax' => $priceIncludesTax,
        'purchase_price' => null,
    ]);

    return $product->refresh();
}

function createCartForUser(User $user, int $productId, int $qty = 1)
{
    return CartCreationPipeline::make()->run(
        CartDto::fromArray([
            'user_id' => $user->getKey(),
            'user_first_name' => $user->first_name,
            'user_last_name' => $user->last_name,
            'user_email' => $user->email,
            'lines' => [
                [
                    'product_id' => $productId,
                    'qty' => $qty,
                ],
            ],
        ])
    );
}

it('applies a category discount through polymorphic relations during cart calculation', function () {
    $taxClass = TaxClass::factory()->create();
    setupTaxEnvironment($taxClass);

    $product = createProduct(100, $taxClass);
    $category = ProductCategory::factory()->create([
        'active' => true,
        'sort_order' => 1,
    ]);
    $product->categories()->attach($category->getKey());

    $category->discounts()->create([
        'type' => DiscountType::Percentage,
        'value' => 10,
        'code' => 'CAT10',
        'active' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $cart = CartCreationPipeline::make()->run(
        CartDto::fromArray([
            'lines' => [
                [
                    'product_id' => $product->getKey(),
                    'qty' => 2,
                ],
            ],
        ])
    )->load('lines');

    $line = $cart->lines->first();

    expect((float) $line->unit_discount)->toBe(10.0)
        ->and((float) $line->discount_amount)->toBe(20.0)
        ->and($line->discount_code)->toBe('CAT10')
        ->and((float) $cart->discount_amount)->toBe(0.0);
});

it('applies discounts only once per cart when the rule is enabled', function () {
    $taxClass = TaxClass::factory()->create();
    setupTaxEnvironment($taxClass);

    $product = createProduct(50, $taxClass);
    $product->discounts()->create([
        'type' => DiscountType::Fixed,
        'value' => 15,
        'code' => 'ONCE15',
        'active' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'rules' => [
            'apply_once_per_cart' => true,
        ],
    ]);

    $cart = CartCreationPipeline::make()->run(
        CartDto::fromArray([
            'lines' => [
                ['product_id' => $product->getKey(), 'qty' => 1],
                ['product_id' => $product->getKey(), 'qty' => 1],
            ],
        ])
    )->load('lines');

    $discountedLines = $cart->lines->filter(fn ($line) => $line->discount_code === 'ONCE15');
    $notDiscountedLines = $cart->lines->filter(fn ($line) => blank($line->discount_code));

    expect($discountedLines)->toHaveCount(1)
        ->and($notDiscountedLines)->toHaveCount(1)
        ->and((float) $discountedLines->first()->unit_discount)->toBe(15.0)
        ->and((float) $notDiscountedLines->first()->unit_discount)->toBe(0.0);
});

it('enforces per-user usage limits after order registration', function () {
    $taxClass = TaxClass::factory()->create();
    setupTaxEnvironment($taxClass);

    $user = User::query()->create([
        'first_name' => 'Mario',
        'last_name' => 'Rossi',
        'email' => 'mario@example.test',
        'phone' => '123456789',
    ]);
    $product = createProduct(80, $taxClass);

    $product->discounts()->create([
        'type' => DiscountType::Percentage,
        'value' => 20,
        'code' => 'USER20',
        'active' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'rules' => [
            'max_uses_per_user' => 1,
        ],
    ]);

    $firstCart = createCartForUser($user, $product->getKey())->load('lines');
    expect($firstCart->lines->first()->discount_code)->toBe('USER20');

    OrderCreationPipeline::make()->run(OrderDto::fromCart($firstCart));

    expect(DiscountApplication::query()
        ->where('user_id', $user->getKey())
        ->count())->toBe(1);

    $secondCart = createCartForUser($user, $product->getKey())->load('lines');
    $secondLine = $secondCart->lines->first();

    expect(blank($secondLine->discount_code))->toBeTrue()
        ->and((float) $secondLine->unit_discount)->toBe(0.0);
});

it('applies cart total discount code at checkout', function () {
    $taxClass = TaxClass::factory()->create();
    setupTaxEnvironment($taxClass);

    $product = createProduct(100, $taxClass);

    $discountModel = config('venditio-core.models.discount');
    $discountModel::query()->create([
        'discountable_type' => null,
        'discountable_id' => null,
        'type' => DiscountType::Percentage,
        'value' => 10,
        'code' => 'CHECKOUT10',
        'active' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'rules' => [
            'apply_to_cart_total' => true,
        ],
    ]);

    $cart = CartCreationPipeline::make()->run(
        CartDto::fromArray([
            'discount_code' => 'CHECKOUT10',
            'lines' => [
                [
                    'product_id' => $product->getKey(),
                    'qty' => 2,
                ],
            ],
        ])
    )->load('lines');

    expect($cart->discount_code)->toBe('CHECKOUT10')
        ->and((float) $cart->sub_total)->toBe(244.0)
        ->and((float) $cart->discount_amount)->toBe(24.4)
        ->and((float) $cart->total_final)->toBe(219.6);
});

it('removes tax from VAT-inclusive inventory price when calculating cart line totals', function () {
    $taxClass = TaxClass::factory()->create();
    setupTaxEnvironment($taxClass);

    $product = createProduct(122, $taxClass, true);

    $cart = CartCreationPipeline::make()->run(
        CartDto::fromArray([
            'lines' => [
                [
                    'product_id' => $product->getKey(),
                    'qty' => 1,
                ],
            ],
        ])
    )->load('lines');

    $line = $cart->lines->first();

    expect((float) $line->unit_final_price)->toBe(122.0)
        ->and((float) $line->unit_final_price_taxable)->toBe(100.0)
        ->and((float) $line->unit_final_price_tax)->toBe(22.0)
        ->and((float) $line->total_final_price)->toBe(122.0);
});

it('recalculates tax correctly for VAT-inclusive prices after discounts', function () {
    $taxClass = TaxClass::factory()->create();
    setupTaxEnvironment($taxClass);

    $product = createProduct(122, $taxClass, true);
    $product->discounts()->create([
        'type' => DiscountType::Fixed,
        'value' => 10,
        'code' => 'GROSS10',
        'active' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $cart = CartCreationPipeline::make()->run(
        CartDto::fromArray([
            'lines' => [
                [
                    'product_id' => $product->getKey(),
                    'qty' => 1,
                ],
            ],
        ])
    )->load('lines');

    $line = $cart->lines->first();

    expect((float) $line->unit_final_price)->toBe(112.0)
        ->and((float) $line->unit_final_price_taxable)->toBe(91.8)
        ->and((float) $line->unit_final_price_tax)->toBe(20.2)
        ->and((float) $line->total_final_price)->toBe(112.0);
});
