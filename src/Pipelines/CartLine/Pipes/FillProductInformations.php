<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Models\Contracts\ProductItem;

class FillProductInformations
{
    public function __invoke(CartLineDtoContract $cartLineDto, Closure $next): Model
    {
        $cartLine = $cartLineDto->getCartLine()->updateTimestamps();

        $productItem = app(ProductItem::class)
            ->with([
                'product',
                'product.categories',
                'inventory',
            ])
            ->firstWhere('id', $cartLineDto->getProductItemId());

        $data = [
            'cart_id' => $cartLineDto->getCart()->getKey(),
            'product_item_id' => $productItem->getKey(),
            'product_name' => $productItem->name,
            'product_sku' => $productItem->sku,
            'unit_price' => $productItem->inventory->price,
            'qty' => $cartLineDto->getQty(),
            'product_item' => $productItem->toArray(),
        ];

        if ($cartLine->getKey()) {
            $data['id'] = $cartLine->getKey();
        }

        $cartLine->fill($data);

        return $next($cartLine);
    }
}
