<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for creating a cart line
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Dto\CartLineDto
 */
class CartLineCreationPipeline extends Pipeline
{
    public function __construct()
    {
        $this->pipes = config('venditio-core.cart_lines.pipelines.create.pipes');
    }
}
