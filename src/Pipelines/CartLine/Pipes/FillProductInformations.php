<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;
use function PictaStudio\VenditioCore\Helpers\Functions\query;

class FillProductInformations
{
    /**
     * In this task the input data is an array of line data
     * - ['product_id', 'qty']
     */
    public function __invoke(CartLineDtoContract $cartLineDto, Closure $next): Model
    {
        // dd($line);

        // dd(
        //     $isSimple,
        //     app(CartValidationRules::class)->getStoreValidationRules(),
        // );
        // dd(
        //     resolve_purchasable_product_model()::query()->get()
        // );

        // $cartLine = get_fresh_model_instance('cart_line');
        $cartLine = $cartLineDto->getCartLine();

        $product = $this->fetchProduct($cartLineDto);

        $cartLine->product()->associate($product);

        $cartLine->fill([
            'unit_price' => $product->inventory->price,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'qty' => $cartLineDto->getQty(),
            'product' => $product->toArray(),
        ]);

        // dd(
        //     $cartLine->toArray(),
        // );

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
