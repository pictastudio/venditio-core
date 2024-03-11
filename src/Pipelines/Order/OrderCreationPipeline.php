<?php

namespace PictaStudio\VenditioCore\Pipelines\Order;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for creating an order.
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Models\Cart
 * the cart will be processed and converted into an order
 */
class OrderCreationPipeline extends Pipeline
{
    protected array $tasks = [];

    public function __construct()
    {
        $this->tasks = config('venditio-core.orders.pipelines.creation');
    }
}
