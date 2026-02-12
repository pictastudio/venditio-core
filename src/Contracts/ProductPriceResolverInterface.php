<?php

namespace PictaStudio\Venditio\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ProductPriceResolverInterface
{
    /**
     * Resolve pricing data for a product.
     *
     * @return array{
     *     unit_price: float|int|string,
     *     purchase_price: float|int|string|null,
     *     price_includes_tax: bool,
     *     price_list: array{id:int|string,name:string}|null
     * }
     */
    public function resolve(Model $product): array;
}
