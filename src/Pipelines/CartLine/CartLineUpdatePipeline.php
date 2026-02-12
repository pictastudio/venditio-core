<?php

namespace PictaStudio\Venditio\Pipelines\CartLine;

use PictaStudio\Venditio\Pipelines\Pipeline;

/**
 * Pipeline for updating a cart line
 *
 * the expected payload is an instance of PictaStudio\Venditio\Dto\CartLineDto
 */
class CartLineUpdatePipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio.cart_line.pipelines.update.pipes');
    }
}
