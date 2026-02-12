<?php

namespace PictaStudio\VenditioCore\Pipelines\Order;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

/**
 * Pipeline for creating an order.
 *
 * the expected payload is an instance of PictaStudio\VenditioCore\Dto\CartDto
 * the cart will be processed and converted into an order
 */
class OrderCreationPipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio-core.order.pipelines.create.pipes');
    }
}
