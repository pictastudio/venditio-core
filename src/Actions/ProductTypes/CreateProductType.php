<?php

namespace PictaStudio\Venditio\Actions\ProductTypes;

use PictaStudio\Venditio\Models\ProductType;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class CreateProductType
{
    public function handle(array $payload): ProductType
    {
        /** @var ProductType $productType */
        $productType = resolve_model('product_type')::create($payload);

        return $productType->refresh();
    }
}
