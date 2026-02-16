<?php

namespace PictaStudio\Venditio\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\Venditio\Dto\Contracts\CartLineDtoContract;
use PictaStudio\Venditio\Pipelines\CartLine\CartLineCreationPipeline;

use function PictaStudio\Venditio\Helpers\Functions\resolve_dto;

class CalculateLines
{
    /**
     * In this task the input data is a fresh instance of the Cart model (not yet persisted to the database)
     * The relation 'lines' is set on the Cart model with the plain array of lines (product_id, qty)
     */
    public function __invoke(Model $cart, Closure $next): Model
    {
        $cart->setRelation(
            'lines',
            $this->calculateLines(
                $cart->getRelation('lines'),
                $cart
            )
        );

        return $next($cart);
    }

    public function calculateLines(Collection $lines, Model $cart): Collection
    {
        return $lines->map(function (mixed $line) use ($cart) {
            $cartLineDto = $line instanceof CartLineDtoContract
                ? $line
                : resolve_dto('cart_line')::fromArray((array) $line);

            $cartLine = $cartLineDto->getCartLine();
            $cartLine->setRelation('cart', $cart);
            $cartLine->cart()->associate($cart);

            return CartLineCreationPipeline::make()->run($cartLineDto);
        });
    }
}
