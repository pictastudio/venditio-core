<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Packages\Simple\Models\Contracts\CartLine;
use PictaStudio\VenditioCore\Packages\Simple\Models\Contracts\ProductItem;
use PictaStudio\VenditioCore\Pipelines\CartLine\CartLineUpdatePipeline;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_dto;

class UpdateLines
{
    public function __invoke(Model $cart, Closure $next): Model
    {
        // $lines = self::calculateLines($cart->getRelation('lines'));

        $lines = $cart->load('lines')->getRelation('lines');
        // $newLines = $cart->getAttribute('lines');

        // dd($lines);

        // foreach ($lines as $key => $line) {
        //     $lines[$key] = CartLineUpdatePipeline::make()->run(
        //         resolve_dto('cart_line')::fromArray([
        //             'cart' => $cart,
        //             'cart_line' => $line,
        //             'product_item_id' => $line['product_item_id'],
        //             'qty' => $line['qty'],
        //         ])
        //     );
        // }

        $lines->map(fn ($line) => (
            CartLineUpdatePipeline::make()->run(
                resolve_dto('cart_line')::fromArray([
                    'cart' => $cart,
                    'cart_line' => $line,
                    'product_item_id' => $line['product_item_id'],
                    'qty' => $line['qty'],
                ])
            )
        ));

        // dd($lines);

        $cart->setRelation('lines', $lines);

        // dd($cart);

        return $next($cart);
    }

    // public static function calculateLines(Collection $lines): Collection
    // {
    //     $productItems = app(ProductItem::class)::query()
    //         ->whereIn('id', $lines->pluck('product_item_id'))
    //         ->with([
    //             'product',
    //             'inventory',
    //         ])
    //         ->get();

    //     return $lines->map(function ($line) use ($productItems) {
    //         $productItem = $productItems->firstWhere('id', $line['product_item_id']);

    //         $cartLine = app(CartLine::class)::findOrFail($line['id']);

    //         $price = $productItem->inventory->price;
    //         $unitDiscount = $price * (10 / 100);

    //         $taxRate = 0; // get 'rate' from 'country_tax_class' after getting the taxClass from the product
    //         $unitFinalPriceTax = 0;
    //         $unitFinalPriceTaxable = $price - $unitDiscount;

    //         return $cartLine->fill([
    //             'product_item_id' => $productItem->getKey(),
    //             'product_name' => $productItem->name,
    //             'product_sku' => $productItem->sku,
    //             'unit_price' => $price,
    //             'unit_discount' => $unitDiscount,
    //             'unit_final_price' => $price - $unitDiscount,
    //             'unit_final_price_tax' => $unitFinalPriceTax, // get 'rate' from 'country_tax_class' after getting the taxClass from the product
    //             'unit_final_price_taxable' => $unitFinalPriceTaxable, // get 'rate' from 'country_tax_class' after getting the taxClass from the product
    //             'qty' => $line['qty'],
    //             'total_final_price' => ($unitFinalPriceTaxable + $unitFinalPriceTax) * $line['qty'],
    //             'tax_rate' => $taxRate,
    //             'product_item' => $productItem->toArray(),
    //         ]);
    //     });
    // }
}
