<?php

namespace PictaStudio\Venditio\Actions\ProductVariants;

use PictaStudio\Venditio\Models\ProductVariant;

class UpdateProductVariant
{
    public function handle(ProductVariant $variant, array $payload): ProductVariant
    {
        $variant->fill($payload);
        $variant->save();

        return $variant->refresh();
    }
}
