<?php

namespace PictaStudio\VenditioCore\Contracts;

use PictaStudio\VenditioCore\Models\Product;

interface ProductSkuGeneratorInterface
{
    public function forProductPayload(array $payload): string;

    public function forVariant(Product $baseProduct, array $options): string;
}
