<?php

namespace PictaStudio\Venditio\Contracts;

use PictaStudio\Venditio\Models\Product;

interface ProductSkuGeneratorInterface
{
    public function forProductPayload(array $payload): string;

    public function forVariant(Product $baseProduct, array $options): string;
}
