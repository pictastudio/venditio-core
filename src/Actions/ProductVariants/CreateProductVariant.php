<?php

namespace PictaStudio\VenditioCore\Actions\ProductVariants;

use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariant;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CreateProductVariant
{
    public function handle(array $payload): ProductVariant
    {
        /** @var ProductVariant $variant */
        $variant = resolve_model('product_variant')::create($payload);

        return $variant->refresh();
    }
}
