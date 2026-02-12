<?php

namespace PictaStudio\Venditio\Actions\ProductVariantOptions;

use PictaStudio\Venditio\Models\ProductVariantOption;

class UpdateProductVariantOption
{
    public function handle(ProductVariantOption $option, array $payload): ProductVariantOption
    {
        $option->fill($payload);
        $option->save();

        return $option->refresh();
    }
}
