<?php

namespace PictaStudio\VenditioCore\Pipelines\Orders;

use PictaStudio\VenditioCore\Pipelines\Pipeline;

class OrderCreationPipeline extends Pipeline
{
    protected array $tasks = [];

    public function __construct()
    {
        $this->tasks = config('venditio-core.orders.pipelines.creation');
    }
}
