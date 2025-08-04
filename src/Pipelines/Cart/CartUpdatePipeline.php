<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for updating a cart.
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Dto\CartDto
 */
class CartUpdatePipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio-core.cart.pipelines.update.pipes');
    }
}
