<?php

namespace PictaStudio\VenditioCore\Actions\Products;

use PictaStudio\VenditioCore\Models\Product;

class CreateProductItemForVariants
{
    /**
     * Create product items for the variants of the product
     *
     * from productVariants and productVariantOptions compute the matrix of productVariantOptions to create productItems
     *
     * @param  array  $productVariants  associative array where the key is the productVariant id and the value is the array of productVariantOptions
     */
    public function execute(Product $product, array $productVariants): void
    {

    }
}
