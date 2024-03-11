<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for creating an cart.
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Dto\StoreCart
 */
class CartCreationPipeline extends Pipeline
{
    protected array $tasks = [];

    public function __construct()
    {
        $this->tasks = config('venditio-core.carts.pipelines.creation.tasks');
    }
}
