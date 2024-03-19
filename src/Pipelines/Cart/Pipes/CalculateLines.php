<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Models\Cart;
use PictaStudio\VenditioCore\Models\Contracts\CartLine;
use PictaStudio\VenditioCore\Models\Contracts\ProductItem;
use PictaStudio\VenditioCore\Pipelines\CartLine\CartLineCreationPipeline;

class CalculateLines
{
    /**
     * In this task the input data is a fresh instance of the Cart model (not yet persisted to the database)
     * The relation 'lines' is set on the Cart model with the plain array of lines (product_item_id, qty)
     */
    public function __invoke(Model $cart, Closure $next): Model
    {
        // $lines = self::calculateLines($cart->getRelation('lines'));
        $lines = $cart->getAttribute('lines');
        unset($cart->lines); // remove the 'lines' attribute from the model (it's not a relation yet)

        foreach ($lines as $key => $line) {
            $lines[$key] = app(CartLineCreationPipeline::class)->run(
                app(CartLineDtoContract::class)::fromArray([
                    'cart' => $cart,
                    'product_item_id' => $line['product_item_id'],
                    'qty' => $line['qty'],
                ])
            );
        }

        $cart->setRelation('lines', $lines);

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

    //         $cartLine = app(CartLine::class);

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
