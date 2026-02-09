<?php

namespace PictaStudio\VenditioCore\Actions\ProductTypes;

use PictaStudio\VenditioCore\Models\ProductType;

class UpdateProductType
{
    public function handle(ProductType $productType, array $payload): ProductType
    {
        $productType->fill($payload);
        $productType->save();

        return $productType->refresh();
    }
}
