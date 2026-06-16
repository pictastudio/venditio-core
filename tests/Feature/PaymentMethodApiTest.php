<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\Venditio\Models\PaymentMethod;

use function Pest\Laravel\{assertDatabaseHas, assertDatabaseMissing, deleteJson, getJson, patchJson, postJson};

uses(RefreshDatabase::class);

it('provides full crud for payment methods', function () {
    $prefix = config('venditio.routes.api.v1.prefix');

    $created = postJson($prefix . '/payment_methods', [
        'code' => 'CARD',
        'name' => 'Credit Card',
        'active' => true,
        'flat_fee' => 2.5,
        'description' => 'Card payments.',
    ])->assertCreated()
        ->assertJsonPath('code', 'CARD')
        ->assertJsonPath('name', 'Credit Card')
        ->assertJsonPath('flat_fee', 2.5)
        ->assertJsonPath('description', 'Card payments.');

    $paymentMethodId = $created->json('id');

    assertDatabaseHas('payment_methods', [
        'id' => $paymentMethodId,
        'code' => 'CARD',
        'name' => 'Credit Card',
        'flat_fee' => 2.50,
    ]);

    getJson($prefix . '/payment_methods/' . $paymentMethodId)
        ->assertOk()
        ->assertJsonPath('id', $paymentMethodId)
        ->assertJsonPath('code', 'CARD');

    getJson($prefix . '/payment_methods?all=1&code=CARD')
        ->assertOk()
        ->assertJsonFragment([
            'id' => $paymentMethodId,
            'code' => 'CARD',
        ]);

    patchJson($prefix . '/payment_methods/' . $paymentMethodId, [
        'name' => 'Credit Card Updated',
        'flat_fee' => 3.5,
        'description' => null,
    ])->assertOk()
        ->assertJsonPath('name', 'Credit Card Updated')
        ->assertJsonPath('flat_fee', 3.5)
        ->assertJsonPath('description', null);

    deleteJson($prefix . '/payment_methods/' . $paymentMethodId)->assertNoContent();

    assertDatabaseMissing('payment_methods', [
        'id' => $paymentMethodId,
        'deleted_at' => null,
    ]);
});

it('validates payment method payloads and unique codes', function () {
    $prefix = config('venditio.routes.api.v1.prefix');
    $paymentMethod = PaymentMethod::factory()->create([
        'code' => 'BANK',
    ]);

    postJson($prefix . '/payment_methods', [
        'code' => 'BANK',
        'name' => 'Bank Transfer',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);

    postJson($prefix . '/payment_methods', [
        'code' => 'NEGATIVE',
        'name' => 'Negative Fee',
        'flat_fee' => -1,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['flat_fee']);

    patchJson($prefix . '/payment_methods/' . $paymentMethod->getKey(), [
        'code' => 'BANK',
    ])->assertOk()
        ->assertJsonPath('code', 'BANK');
});

it('filters payment methods and supports soft delete filters', function () {
    $prefix = config('venditio.routes.api.v1.prefix');
    $active = PaymentMethod::factory()->create([
        'code' => 'ACTIVE',
        'active' => true,
        'flat_fee' => 1,
    ]);
    $inactive = PaymentMethod::factory()->create([
        'code' => 'INACTIVE',
        'active' => false,
        'flat_fee' => 5,
    ]);
    $deleted = PaymentMethod::factory()->create([
        'code' => 'DELETED',
    ]);
    $deleted->delete();

    getJson($prefix . '/payment_methods?all=1&active=0')
        ->assertOk()
        ->assertJsonFragment(['id' => $inactive->getKey()])
        ->assertJsonMissing(['id' => $active->getKey()]);

    getJson($prefix . '/payment_methods?all=1&flat_fee=1')
        ->assertOk()
        ->assertJsonFragment(['id' => $active->getKey()])
        ->assertJsonMissing(['id' => $inactive->getKey()]);

    getJson($prefix . '/payment_methods?all=1&only_trashed=1')
        ->assertOk()
        ->assertJsonFragment(['id' => $deleted->getKey()])
        ->assertJsonMissing(['id' => $active->getKey()]);
});
