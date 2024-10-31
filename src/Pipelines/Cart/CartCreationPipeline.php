<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for creating a cart
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Dto\CartDto
 */
class CartCreationPipeline extends Pipeline
{
    public function __construct()
    {
        $this->pipes = config('venditio-core.carts.pipelines.create.pipes');
    }
}
