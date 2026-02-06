<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Actions\Products;

use PictaStudio\VenditioCore\Packages\Simple\Models\Product;

class CreateProductVariants
{
    /**
     * Create variant products for the given product
     *
     * from productVariants and productVariantOptions compute the matrix of productVariantOptions to create variants
     *
     * @param  array  $productVariants  associative array where the key is the productVariant id and the value is the array of productVariantOptions
     */
    public function execute(Product $product, array $productVariants): void
    {

    }
}
