<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, Schema};
use PictaStudio\Venditio\Contracts\{OrderInvoiceDataFactoryInterface, OrderInvoiceRendererInterface, OrderInvoiceTemplateInterface};
use PictaStudio\Venditio\Enums\OrderStatus;
use PictaStudio\Venditio\Models\{Order, OrderLine, TaxClass};

use function Pest\Laravel\get;

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

if (!class_exists('TestOrderInvoiceDataFactory')) {
    class TestOrderInvoiceDataFactory implements OrderInvoiceDataFactoryInterface
    {
        public function make(Order $order): array
        {
            return [
                'receipt_number' => 'custom-' . $order->getKey(),
                'document_title' => 'Custom',
            ];
        }
    }
}

if (!class_exists('TestOrderInvoiceTemplate')) {
    class TestOrderInvoiceTemplate implements OrderInvoiceTemplateInterface
    {
        public function render(array $invoice): string
        {
            return '<html><body><h1>' . data_get($invoice, 'document_title') . '</h1></body></html>';
        }
    }
}

if (!class_exists('TestOrderInvoiceRenderer')) {
    class TestOrderInvoiceRenderer implements OrderInvoiceRendererInterface
    {
        public function render(string $document, array $invoice): string
        {
            return '%PDF-CUSTOM-RENDERER';
        }
    }
}

function createOrderWithLineForInvoice(): Order
{
    $taxClassId = TaxClass::factory()->create()->getKey();

    DB::table('products')->insert([
        'parent_id' => null,
        'brand_id' => null,
        'product_type_id' => null,
        'tax_class_id' => $taxClassId,
        'name' => 'Invoice Product',
        'slug' => 'invoice-product',
        'status' => 'published',
        'active' => true,
        'new' => true,
        'in_evidence' => false,
        'sku' => 'INV-PROD-001',
        'ean' => null,
        'visible_from' => null,
        'visible_until' => null,
        'description' => null,
        'description_short' => null,
        'images' => null,
        'files' => null,
        'measuring_unit' => null,
        'qty_for_unit' => null,
        'length' => null,
        'width' => null,
        'height' => null,
        'weight' => null,
        'metadata' => null,
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => null,
    ]);

    $productId = (int) DB::table('products')
        ->where('sku', 'INV-PROD-001')
        ->value('id');

    $order = Order::query()->create([
        'identifier' => '2129-5517',
        'status' => OrderStatus::Pending,
        'sub_total_taxable' => 20,
        'sub_total_tax' => 4.4,
        'sub_total' => 24.4,
        'shipping_fee' => 0,
        'payment_fee' => 0,
        'discount_amount' => 0,
        'total_final' => 24.4,
        'user_first_name' => 'Francesco',
        'user_last_name' => 'Mecchi',
        'user_email' => 'fra9879@gmail.com',
        'addresses' => [
            'billing' => [
                'street' => 'Viale Sicilia, 76',
                'postal_code' => '37138',
                'city' => 'Verona',
                'province_name' => 'Verona',
                'country_name' => 'Italia',
            ],
        ],
        'approved_at' => now(),
    ]);

    OrderLine::query()->create([
        'order_id' => $order->getKey(),
        'product_id' => $productId,
        'discount_id' => null,
        'product_name' => 'Cursor Pro',
        'product_sku' => 'CURSOR-PRO',
        'discount_code' => null,
        'discount_amount' => 0,
        'unit_price' => 20,
        'purchase_price' => 10,
        'unit_discount' => 0,
        'unit_final_price' => 20,
        'unit_final_price_tax' => 4.4,
        'unit_final_price_taxable' => 20,
        'qty' => 1,
        'total_final_price' => 24.4,
        'tax_rate' => 22,
        'product_data' => [
            'period' => '19 feb 2026-19 mar 2026',
        ],
    ]);

    return $order->refresh();
}

it('returns order invoice as pdf', function () {
    $order = createOrderWithLineForInvoice();
    $prefix = config('venditio.routes.api.v1.prefix');

    $response = get($prefix . '/orders/' . $order->getKey() . '/invoice')
        ->assertOk();

    expect($response->headers->get('content-type'))->toContain('application/pdf')
        ->and($response->headers->get('content-disposition'))->toContain('inline')
        ->and(mb_substr((string) $response->getContent(), 0, 4))->toBe('%PDF');
});

it('supports attachment disposition for invoice download', function () {
    $order = createOrderWithLineForInvoice();
    $prefix = config('venditio.routes.api.v1.prefix');

    $response = get($prefix . '/orders/' . $order->getKey() . '/invoice?download=1')
        ->assertOk();

    expect($response->headers->get('content-disposition'))->toContain('attachment');
});

it('allows swapping invoice implementations', function () {
    app()->singleton(OrderInvoiceDataFactoryInterface::class, TestOrderInvoiceDataFactory::class);
    app()->singleton(OrderInvoiceTemplateInterface::class, TestOrderInvoiceTemplate::class);
    app()->singleton(OrderInvoiceRendererInterface::class, TestOrderInvoiceRenderer::class);

    $order = createOrderWithLineForInvoice();
    $prefix = config('venditio.routes.api.v1.prefix');

    $response = get($prefix . '/orders/' . $order->getKey() . '/invoice')
        ->assertOk();

    expect((string) $response->getContent())->toBe('%PDF-CUSTOM-RENDERER');
});

it('renders invoice when pdf temp directory config is empty', function () {
    config()->set('venditio.order.invoice.pdf.temp_dir', '');
    config()->set('venditio.order.invoice.pdf.font_cache_dir', '');

    $order = createOrderWithLineForInvoice();
    $prefix = config('venditio.routes.api.v1.prefix');

    $response = get($prefix . '/orders/' . $order->getKey() . '/invoice')
        ->assertOk();

    expect($response->headers->get('content-type'))->toContain('application/pdf')
        ->and(mb_substr((string) $response->getContent(), 0, 4))->toBe('%PDF');
});

it('renders invoice for orders with empty addresses and nullable customer fields', function () {
    $taxClassId = TaxClass::factory()->create()->getKey();

    DB::table('products')->insert([
        'parent_id' => null,
        'brand_id' => null,
        'product_type_id' => null,
        'tax_class_id' => $taxClassId,
        'name' => 'Invoice Product 2',
        'slug' => 'invoice-product-2',
        'status' => 'published',
        'active' => true,
        'new' => true,
        'in_evidence' => false,
        'sku' => 'INV-PROD-002',
        'ean' => null,
        'visible_from' => null,
        'visible_until' => null,
        'description' => null,
        'description_short' => null,
        'images' => null,
        'files' => null,
        'measuring_unit' => null,
        'qty_for_unit' => null,
        'length' => null,
        'width' => null,
        'height' => null,
        'weight' => null,
        'metadata' => null,
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => null,
    ]);

    $productId = (int) DB::table('products')
        ->where('sku', 'INV-PROD-002')
        ->value('id');

    $order = Order::query()->create([
        'identifier' => '2026-02-000001',
        'status' => OrderStatus::Completed,
        'sub_total_taxable' => 84.98,
        'sub_total_tax' => 18.7,
        'sub_total' => 457.41,
        'shipping_fee' => 0,
        'payment_fee' => 0,
        'discount_amount' => 0,
        'total_final' => 457.41,
        'user_first_name' => null,
        'user_last_name' => null,
        'user_email' => null,
        'addresses' => [
            'billing' => [],
            'shipping' => [],
        ],
        'approved_at' => now(),
    ]);

    OrderLine::query()->create([
        'order_id' => $order->getKey(),
        'product_id' => $productId,
        'discount_id' => null,
        'product_name' => 'Cursor Pro',
        'product_sku' => 'CURSOR-PRO',
        'discount_code' => null,
        'discount_amount' => 0,
        'unit_price' => 20,
        'purchase_price' => 10,
        'unit_discount' => 0,
        'unit_final_price' => 20,
        'unit_final_price_tax' => 4.4,
        'unit_final_price_taxable' => 20,
        'qty' => 1,
        'total_final_price' => 24.4,
        'tax_rate' => 22,
        'product_data' => [],
    ]);

    $prefix = config('venditio.routes.api.v1.prefix');

    $response = get($prefix . '/orders/' . $order->getKey() . '/invoice')
        ->assertOk();

    expect($response->headers->get('content-type'))->toContain('application/pdf')
        ->and(mb_substr((string) $response->getContent(), 0, 4))->toBe('%PDF');
});
