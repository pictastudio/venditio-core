<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Pipelines\CartLine\CartLineUpdatePipeline;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_dto;

class UpdateLines
{
    public function __invoke(Model $cart, Closure $next): Model
    {
        $lines = $cart->load('lines')->getRelation('lines');

        $lines->map(fn ($line) => (
            CartLineUpdatePipeline::make()->run(
                resolve_dto('cart_line')::fromArray([
                    'cart' => $cart,
                    'cart_line' => $line,
                    'product_id' => $line['product_id'] ?? null,
                    'qty' => $line['qty'],
                ])
            )
        ));

        $cart->setRelation('lines', $lines);

        return $next($cart);
    }
}
