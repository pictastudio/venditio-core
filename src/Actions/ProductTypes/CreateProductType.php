<?php

namespace PictaStudio\VenditioCore\Actions\ProductTypes;

use PictaStudio\VenditioCore\Models\ProductType;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CreateProductType
{
    public function handle(array $payload): ProductType
    {
        /** @var ProductType $productType */
        $productType = resolve_model('product_type')::create($payload);

        return $productType->refresh();
    }
}
