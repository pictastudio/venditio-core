<?php

namespace PictaStudio\Venditio\Actions\ProductVariants;

use PictaStudio\Venditio\Models\ProductVariant;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class CreateProductVariant
{
    public function handle(array $payload): ProductVariant
    {
        /** @var ProductVariant $variant */
        $variant = resolve_model('product_variant')::create($payload);

        return $variant->refresh();
    }
}
