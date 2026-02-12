<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PictaStudio\Venditio\Models\Product;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

describe('applies scopes correctly', function () {
    it('does not retrieve products that are inactive', function () {
        $productInactive = Product::factory()->inactive()->create();
        $productInactive = Product::factory()->create();

        $products = Product::all();

        expect($products)->not->toContain($productInactive);

        getJson(config('venditio.routes.api.v1.prefix'))->assertJsonMissing(['id' => $productInactive->getKey()]);
    })->todo()->skip();
});
