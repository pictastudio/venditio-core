<?php

namespace PictaStudio\VenditioCore\Pipelines;

use Illuminate\Support\Facades\Pipeline as PipelineFacade;

abstract class Pipeline
{
    protected array $tasks = [];

    public function run(object $payload): mixed
    {
        return PipelineFacade::send(
            passable: $payload,
        )->through(
            pipes: $this->tasks,
        )->thenReturn();
    }
}
