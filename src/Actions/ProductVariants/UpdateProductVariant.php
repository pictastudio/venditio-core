<?php

namespace PictaStudio\VenditioCore\Actions\ProductVariants;

use PictaStudio\VenditioCore\Models\ProductVariant;

class UpdateProductVariant
{
    public function handle(ProductVariant $variant, array $payload): ProductVariant
    {
        $variant->fill($payload);
        $variant->save();

        return $variant->refresh();
    }
}
