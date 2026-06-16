<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PictaStudio\Venditio\Models\{Currency, Order, OrderLine, Product, ShippingMethod, ShippingStatus, ShippingZone, User};

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('keeps the orders index lean by default', function () {
    [$order] = createOrderIndexFixture();
    $queries = [];

    DB::listen(function ($query) use (&$queries): void {
        $queries[] = $query->sql;
    });

    getJson(config('venditio.routes.api.v1.prefix') . '/orders?all=1&id[]=' . $order->getKey())
        ->assertOk()
        ->assertJsonPath('0.id', $order->getKey())
        ->assertJsonPath('0.identifier', (string) $order->identifier)
        ->assertJsonPath('0.addresses.billing.first_name', 'Lean')
        ->assertJsonPath('0.shipping_method_data.id', $order->shipping_method_id)
        ->assertJsonPath('0.shipping_zone_data.id', $order->shipping_zone_id)
        ->assertJsonMissingPath('0.lines')
        ->assertJsonMissingPath('0.shipping_method')
        ->assertJsonMissingPath('0.shipping_status')
        ->assertJsonMissingPath('0.shipping_zone')
        ->assertJsonMissingPath('0.user');

    $relationQueries = collect($queries)
        ->map(fn (string $sql): string => mb_strtolower($sql))
        ->filter(fn (string $sql): bool => str_contains($sql, 'order_lines')
            || str_contains($sql, 'shipping_methods')
            || str_contains($sql, 'shipping_zones'));

    expect($relationQueries->all())->toBe([]);
});

it('loads orders index relations when requested through includes', function () {
    [$order, $orderLine, $shippingMethod, $shippingStatus, $shippingZone, $user] = createOrderIndexFixture();

    getJson(config('venditio.routes.api.v1.prefix') . '/orders?all=1&id[]=' . $order->getKey() . '&include=lines,shipping_method,shipping_status,shipping_zone,user')
        ->assertOk()
        ->assertJsonPath('0.id', $order->getKey())
        ->assertJsonPath('0.lines.0.id', $orderLine->getKey())
        ->assertJsonPath('0.shipping_method.id', $shippingMethod->getKey())
        ->assertJsonPath('0.shipping_status.id', $shippingStatus->getKey())
        ->assertJsonPath('0.shipping_zone.id', $shippingZone->getKey())
        ->assertJsonPath('0.user.id', $user->getKey());
});

it('searches orders by identifier and user snapshot fields', function () {
    $defaultAttributes = [
        'addresses' => [
            'billing' => [],
            'shipping' => [],
        ],
    ];

    $identifierMatch = Order::factory()->create(array_merge($defaultAttributes, [
        'identifier' => 'ORD-NEEDLE-001',
        'user_first_name' => 'Alba',
        'user_last_name' => 'Rossi',
        'user_email' => 'alba@example.test',
    ]));
    $firstNameMatch = Order::factory()->create(array_merge($defaultAttributes, [
        'identifier' => 'ORD-PLAIN-002',
        'user_first_name' => 'Needle',
        'user_last_name' => 'Bianchi',
        'user_email' => 'bianchi@example.test',
    ]));
    $lastNameMatch = Order::factory()->create(array_merge($defaultAttributes, [
        'identifier' => 'ORD-PLAIN-003',
        'user_first_name' => 'Carlo',
        'user_last_name' => 'Hayneedle',
        'user_email' => 'carlo@example.test',
    ]));
    $emailMatch = Order::factory()->create(array_merge($defaultAttributes, [
        'identifier' => 'ORD-PLAIN-004',
        'user_first_name' => 'Dora',
        'user_last_name' => 'Verdi',
        'user_email' => 'shopper.needle@example.test',
    ]));
    $nonMatch = Order::factory()->create(array_merge($defaultAttributes, [
        'identifier' => 'ORD-PLAIN-005',
        'user_first_name' => 'Elena',
        'user_last_name' => 'Neri',
        'user_email' => 'elena@example.test',
    ]));

    $response = getJson(config('venditio.routes.api.v1.prefix') . '/orders?all=1&search=' . urlencode('nEeDlE'))
        ->assertOk();

    $ids = collect($response->json())
        ->pluck('id')
        ->all();

    expect($ids)->toEqualCanonicalizing([
        $identifierMatch->getKey(),
        $firstNameMatch->getKey(),
        $lastNameMatch->getKey(),
        $emailMatch->getKey(),
    ])->not->toContain($nonMatch->getKey());
});

function createOrderIndexFixture(): array
{
    $user = User::factory()->create([
        'first_name' => 'Lean',
        'last_name' => 'Customer',
    ]);
    $shippingMethod = ShippingMethod::factory()->create();
    $shippingStatus = ShippingStatus::factory()->create();
    $shippingZone = ShippingZone::factory()->create();
    $product = Product::factory()->create();
    $currency = Currency::query()->firstOrFail();

    $order = Order::factory()->create([
        'user_id' => $user->getKey(),
        'shipping_method_id' => $shippingMethod->getKey(),
        'shipping_status_id' => $shippingStatus->getKey(),
        'shipping_zone_id' => $shippingZone->getKey(),
        'addresses' => [
            'billing' => [
                'first_name' => 'Lean',
                'last_name' => 'Customer',
            ],
            'shipping' => [],
        ],
        'shipping_method_data' => [
            'id' => $shippingMethod->getKey(),
            'name' => $shippingMethod->name,
        ],
        'shipping_zone_data' => [
            'id' => $shippingZone->getKey(),
            'name' => $shippingZone->name,
        ],
    ]);

    $orderLine = OrderLine::query()->create([
        'order_id' => $order->getKey(),
        'product_id' => $product->getKey(),
        'currency_id' => $currency->getKey(),
        'product_name' => 'Lean Index Product',
        'product_sku' => 'LEAN-INDEX-001',
        'unit_price' => 100,
        'unit_final_price' => 100,
        'unit_final_price_tax' => 0,
        'unit_final_price_taxable' => 100,
        'qty' => 1,
        'total_final_price' => 100,
        'tax_rate' => 0,
        'product_data' => [],
    ]);

    return [$order, $orderLine, $shippingMethod, $shippingStatus, $shippingZone, $user];
}
