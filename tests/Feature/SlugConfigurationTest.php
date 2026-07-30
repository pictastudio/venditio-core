<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\Translatable\Locales;
use PictaStudio\Venditio\Models\{ProductType, User};
use PictaStudio\Venditio\Support\SlugConfiguration;

use function Pest\Laravel\{assertDatabaseHas, patchJson, postJson};

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('translatable.locales', ['en', 'it']);
    app()->setLocale('en');
    app(Locales::class)->load();
});

it('preserves the legacy slug configuration defaults', function () {
    expect(SlugConfiguration::regenerateOnUpdate('product'))->toBeTrue()
        ->and(SlugConfiguration::editableViaApi('product'))->toBeTrue()
        ->and(SlugConfiguration::regenerateOnUpdate('wishlist'))->toBeTrue()
        ->and(SlugConfiguration::editableViaApi('wishlist'))->toBeTrue();
});

it('resolves resource slug overrides before global values', function () {
    config()->set('venditio.slugs.regenerate_on_update', false);
    config()->set('venditio.slugs.editable_via_api', false);
    config()->set('venditio.slugs.resources.product_type', [
        'regenerate_on_update' => true,
        'editable_via_api' => true,
    ]);

    expect(SlugConfiguration::regenerateOnUpdate('product'))->toBeFalse()
        ->and(SlugConfiguration::editableViaApi('product'))->toBeFalse()
        ->and(SlugConfiguration::regenerateOnUpdate('product_type'))->toBeTrue()
        ->and(SlugConfiguration::editableViaApi('product_type'))->toBeTrue();
});

it('keeps generated slugs stable when update regeneration is disabled', function () {
    config()->set('venditio.slugs.regenerate_on_update', false);

    $response = postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'name' => 'Original type',
        'active' => true,
    ])->assertCreated();

    $productTypeId = $response->json('id');

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'name' => 'Updated type',
    ])
        ->assertOk()
        ->assertJsonPath('slug', 'original-type');

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'name' => 'Another type',
        'slug' => 'manual-stable-slug',
    ])
        ->assertOk()
        ->assertJsonPath('slug', 'manual-stable-slug');

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'name' => 'Final type',
    ])
        ->assertOk()
        ->assertJsonPath('slug', 'manual-stable-slug');
});

it('keeps translated slugs stable when update regeneration is disabled', function () {
    config()->set('venditio.slugs.regenerate_on_update', false);

    $response = postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'active' => true,
        'en' => ['name' => 'English type'],
        'it' => ['name' => 'Tipo italiano'],
    ])->assertCreated();

    $productTypeId = $response->json('id');

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'en' => ['name' => 'Updated English type'],
        'it' => ['name' => 'Tipo italiano aggiornato'],
    ])->assertOk();

    assertDatabaseHas('translations', [
        'translatable_type' => (new ProductType)->getMorphClass(),
        'translatable_id' => $productTypeId,
        'locale' => 'en',
        'attribute' => 'slug',
        'value' => 'english-type',
    ]);

    assertDatabaseHas('translations', [
        'translatable_type' => (new ProductType)->getMorphClass(),
        'translatable_id' => $productTypeId,
        'locale' => 'it',
        'attribute' => 'slug',
        'value' => 'tipo-italiano',
    ]);

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'it' => [
            'name' => 'Ancora aggiornato',
            'slug' => 'italiano-stabile',
        ],
    ])->assertOk();

    assertDatabaseHas('translations', [
        'translatable_type' => (new ProductType)->getMorphClass(),
        'translatable_id' => $productTypeId,
        'locale' => 'it',
        'attribute' => 'slug',
        'value' => 'italiano-stabile',
    ]);
});

it('lets an explicit api slug win for one save before automatic regeneration resumes', function () {
    $response = postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'name' => 'Original type',
        'slug' => 'stable-url',
        'active' => true,
    ])->assertCreated();

    $productTypeId = $response->json('id');

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'name' => 'Updated type',
        'slug' => 'stable-url',
    ])
        ->assertOk()
        ->assertJsonPath('slug', 'stable-url');

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'name' => 'Regenerated type',
    ])
        ->assertOk()
        ->assertJsonPath('slug', 'regenerated-type');
});

it('preserves explicit localized slugs while generating omitted locales', function () {
    $response = postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'active' => true,
        'en' => [
            'name' => 'English type',
            'slug' => 'stable-english',
        ],
        'it' => [
            'name' => 'Tipo italiano',
            'slug' => 'italiano-manuale',
        ],
    ])->assertCreated();

    $productTypeId = $response->json('id');

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'en' => [
            'name' => 'English type updated',
            'slug' => 'stable-english',
        ],
        'it' => [
            'name' => 'Tipo italiano aggiornato',
        ],
    ])->assertOk();

    assertDatabaseHas('translations', [
        'translatable_type' => (new ProductType)->getMorphClass(),
        'translatable_id' => $productTypeId,
        'locale' => 'en',
        'attribute' => 'slug',
        'value' => 'stable-english',
    ]);

    assertDatabaseHas('translations', [
        'translatable_type' => (new ProductType)->getMorphClass(),
        'translatable_id' => $productTypeId,
        'locale' => 'it',
        'attribute' => 'slug',
        'value' => 'tipo-italiano-aggiornato',
    ]);
});

it('rejects api slug input when editability is disabled', function () {
    $response = postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'name' => 'Existing type',
        'active' => true,
    ])->assertCreated();

    $productTypeId = $response->json('id');
    config()->set('venditio.slugs.editable_via_api', false);

    patchJson(config('venditio.routes.api.v1.prefix') . "/product_types/{$productTypeId}", [
        'slug' => 'forbidden-update',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);

    postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'name' => 'Top-level slug',
        'slug' => 'forbidden',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);

    postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'translations' => [
            'en' => [
                'name' => 'Wrapped type',
                'slug' => 'forbidden',
            ],
        ],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['en.slug']);

    postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'name:en' => 'Flat type',
        'slug:en' => 'forbidden',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug:en']);

    postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'active' => true,
        'en' => ['name' => 'Generated English type'],
        'it' => ['name' => 'Tipo italiano generato'],
    ])
        ->assertCreated()
        ->assertJsonPath('slug', 'generated-english-type');
});

it('accepts resource editability overrides and still generates non-editable wishlist slugs', function () {
    config()->set('venditio.slugs.editable_via_api', false);
    config()->set('venditio.slugs.resources.product_type.editable_via_api', true);

    postJson(config('venditio.routes.api.v1.prefix') . '/product_types', [
        'name' => 'Custom type',
        'slug' => 'custom-type',
        'active' => true,
    ])
        ->assertCreated()
        ->assertJsonPath('slug', 'custom-type');

    $user = User::factory()->create();

    postJson(config('venditio.routes.api.v1.prefix') . '/wishlists', [
        'user_id' => $user->getKey(),
        'name' => 'Generated wishlist',
    ])
        ->assertCreated()
        ->assertJsonPath('slug', 'generated-wishlist');

    postJson(config('venditio.routes.api.v1.prefix') . '/wishlists', [
        'user_id' => $user->getKey(),
        'name' => 'Forbidden wishlist',
        'slug' => 'forbidden',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);
});
