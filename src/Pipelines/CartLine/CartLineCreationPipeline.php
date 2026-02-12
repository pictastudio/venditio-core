<?php

namespace PictaStudio\Venditio\Pipelines\CartLine;

use PictaStudio\Venditio\Pipelines\Pipeline;

/**
 * Pipeline for creating a cart line
 *
 * the expected payload is an instance of PictaStudio\Venditio\Dto\CartLineDto
 */
class CartLineCreationPipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio.cart_line.pipelines.create.pipes');
    }
}
