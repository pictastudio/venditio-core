<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for updating a cart line
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Dto\CartLineDto
 */
class CartLineUpdatePipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio-core.cart_line.pipelines.update.pipes');
    }
}
