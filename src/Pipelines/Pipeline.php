<?php

namespace PictaStudio\VenditioCore\Pipelines;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline as PipelineFacade;
use Illuminate\Support\Traits\Conditionable;
use PictaStudio\VenditioCore\Traits\HasMakeConstructor;

abstract class Pipeline
{
    use HasMakeConstructor;
    use Conditionable;

    protected array $pipes = [];

    public function run(object|array $payload): mixed
    {
        return DB::transaction(fn () => (
            PipelineFacade::send($payload)
                ->through($this->getPipes())
                ->thenReturn()
        ));
    }

    public function addPipe(string $pipe, ?int $index): static
    {
        if ($index) {
            array_splice($this->pipes, $index, 0, $pipe);
        } else {
            $this->pipes[] = $pipe;
        }

        return $this;
    }

    public function removePipe(string $pipe): static
    {
        $this->pipes = array_filter($this->pipes, fn (string $p) => $p !== $pipe);

        return $this;
    }

    public function getPipes(): array
    {
        return $this->pipes;
    }
}
