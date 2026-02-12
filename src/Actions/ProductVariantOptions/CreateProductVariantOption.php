<?php

namespace PictaStudio\VenditioCore\Actions\ProductVariantOptions;

use PictaStudio\VenditioCore\Models\ProductVariantOption;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CreateProductVariantOption
{
    public function handle(array $payload): ProductVariantOption
    {
        /** @var ProductVariantOption $option */
        $option = resolve_model('product_variant_option')::create($payload);

        return $option->refresh();
    }
}
