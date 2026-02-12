<?php

namespace PictaStudio\Venditio\Actions\ProductTypes;

use PictaStudio\Venditio\Models\ProductType;

class UpdateProductType
{
    public function handle(ProductType $productType, array $payload): ProductType
    {
        $productType->fill($payload);
        $productType->save();

        return $productType->refresh();
    }
}
