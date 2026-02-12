<?php

namespace PictaStudio\Venditio\Pipelines\Cart;

use PictaStudio\Venditio\Pipelines\Pipeline;

/**
 * Pipeline for creating a cart
 *
 * the expected payload is an instance of PictaStudio\Venditio\Dto\CartDto
 */
class CartCreationPipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio.cart.pipelines.create.pipes');
    }
}
