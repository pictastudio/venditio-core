<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Packages\Simple\Models\CartLine;
use PictaStudio\VenditioCore\Packages\Simple\Models\Contracts\ProductItem;
use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;
use function PictaStudio\VenditioCore\Helpers\Functions\query;
use function PictaStudio\VenditioCore\Helpers\Functions\query_purchasable_product_model;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_purchasable_product_model;

class FillProductInformations
{
    /**
     * In this task the input data is an array of line data
     * - if simple package is used -> ['product_id', 'qty']
     * - if advanced package is used -> ['product_item_id', 'qty']
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

        // if simple package is used the correct relation 'product' otherwise 'product_item'
        if (VenditioCore::isSimple() && $cartLine instanceof CartLine) {
            $cartLine->product()->associate($product);
        } else {
            $cartLine->productItem()->associate($product);
        }

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

        // $data = [
        //     // 'cart_id' => $cartLineDto->getCart()->getKey(),
        //     'product_item_id' => $productItem->getKey(),
        //     'product_name' => $productItem->name,
        //     'product_sku' => $productItem->sku,
        //     'unit_price' => $productItem->inventory->price,
        //     'qty' => $cartLineDto->getQty(),
        //     'product_item' => $productItem->toArray(),
        // ];

        // if ($cartLine->getKey()) {
        //     $data['id'] = $cartLine->getKey();
        // }

        // $cartLineId = Arr::get($line, 'id');

        // if (filled($cartLineId)) {
        //     $cartLine->setAttribute('id', $cartLineId);
        // }

        // dd($cartLine->toArray());

        // $cartLine->fill($data);

        return $next($cartLine);
    }

    private function fetchProduct(CartLineDtoContract $cartLineDto): Model
    {
        $productId = $cartLineDto->getPurchasableModelId();

        $relationsToLoad = [
            'inventory',
            'categories',
            'brand',
        ];

        if (VenditioCore::isAdvanced()) {
            $relationsToLoad = [
                'inventory',
                'product',
                'product.categories',
                'product.brand',
            ];
        }

        return query_purchasable_product_model()
            ->with($relationsToLoad)
            ->firstWhere('id', $productId);
    }

    private function checkPayloadValidity(array $line, bool $isSimple): void
    {
        if ($isSimple) {
            if (!Arr::has($line, 'product_id')) {
                throw new Exception('The key "product_id" is missing from the line data');
            }
        } else {
            if (!Arr::has($line, 'product_item_id')) {
                throw new Exception('The key "product_item_id" is missing from the line data');
            }
        }

        if (!Arr::has($line, 'qty')) {
            throw new Exception('The key "qty" is missing from the line data');
        }
    }
}
