<?php

namespace PictaStudio\Venditio\Pipelines\Order;

use PictaStudio\Venditio\Pipelines\Pipeline;

/**
 * Pipeline for creating an order.
 *
 * the expected payload is an instance of PictaStudio\Venditio\Dto\CartDto
 * the cart will be processed and converted into an order
 */
class OrderCreationPipeline extends Pipeline
{
    public function getPipes(): array
    {
        return config('venditio.order.pipelines.create.pipes');
    }
}
