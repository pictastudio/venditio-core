<?php

namespace PictaStudio\Venditio\Actions\ProductVariantOptions;

use PictaStudio\Venditio\Models\ProductVariantOption;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class CreateProductVariantOption
{
    public function handle(array $payload): ProductVariantOption
    {
        /** @var ProductVariantOption $option */
        $option = resolve_model('product_variant_option')::create($payload);

        return $option->refresh();
    }
}
