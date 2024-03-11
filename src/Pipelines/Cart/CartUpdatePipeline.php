<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for updating a cart.
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Dto\StoreCart
 */
class CartUpdatePipeline extends Pipeline
{
    protected array $tasks = [];

    public function __construct()
    {
        $this->tasks = config('venditio-core.carts.pipelines.update.tasks');
    }
}
