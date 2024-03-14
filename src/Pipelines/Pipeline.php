<?php

namespace PictaStudio\VenditioCore\Pipelines;

use Illuminate\Support\Facades\Pipeline as PipelineFacade;

abstract class Pipeline
{
    protected array $pipes = [];

    public function run(object|array $payload): mixed
    {
        return PipelineFacade::send($payload)
            ->through($this->pipes)
            ->thenReturn();
    }
}
