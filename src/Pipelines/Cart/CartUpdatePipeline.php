<?php

namespace PictaStudio\Venditio\Pipelines\Cart;

use PictaStudio\Venditio\Pipelines\Pipeline;

/**
 * Pipeline for updating a cart.
 *
 * the expected payload is an instance of PictaStudio\Venditio\Dto\CartDto
 */
class CartUpdatePipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio.cart.pipelines.update.pipes');
    }
}
