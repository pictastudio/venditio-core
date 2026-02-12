<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use function PictaStudio\VenditioCore\Helpers\Functions\query;

class FillProductInformations
{
    /**
     * In this task the input data is an array of line data
     * - ['product_id', 'qty']
     */
    public function __invoke(CartLineDtoContract $cartLineDto, Closure $next): Model
    {
        $cartLine = $cartLineDto->getCartLine();

        $product = $this->fetchProduct($cartLineDto);

        $cartLine->product()->associate($product);

        $cartLine->fill([
            'unit_price' => $product->inventory->price,
            'purchase_price' => $product->inventory->purchase_price,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'qty' => $cartLineDto->getQty(),
            'product_data' => $product->toArray(),
        ]);

        return $next($cartLine);
    }

    private function fetchProduct(CartLineDtoContract $cartLineDto): Model
    {
        $productId = $cartLineDto->getPurchasableModelId();

        return query('product')
            ->with([
                'inventory',
                'categories',
                'brand',
                'productType',
                'variantOptions',
                'parent',
            ])
            ->firstWhere('id', $productId);
    }

    private function checkPayloadValidity(array $line): void
    {
        if (!Arr::has($line, 'product_id')) {
            throw new Exception('The key "product_id" is missing from the line data');
        }

        if (!Arr::has($line, 'qty')) {
            throw new Exception('The key "qty" is missing from the line data');
        }
    }
}
