<?php

namespace PictaStudio\VenditioCore\Actions\ProductVariantOptions;

use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariantOption;

class UpdateProductVariantOption
{
    public function handle(ProductVariantOption $option, array $payload): ProductVariantOption
    {
        $option->fill($payload);
        $option->save();

        return $option->refresh();
    }
}
