<?php

namespace PictaStudio\Venditio\Pipelines;

use Illuminate\Support\Facades\{DB, Pipeline as PipelineFacade};
use Illuminate\Support\Traits\Conditionable;
use PictaStudio\Venditio\Traits\HasMakeConstructor;

abstract class Pipeline
{
    use Conditionable;
    use HasMakeConstructor;

    protected array $pipes = [];

    abstract public function getPipes(): array;

    public function run(mixed $payload): mixed
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

    public function prependPipe(string $pipe): static
    {
        return $this->addPipe($pipe, 0);
    }

    public function appendPipe(string $pipe): static
    {
        return $this->addPipe($pipe, null);
    }

    public function removePipe(string $pipe): static
    {
        $this->pipes = array_filter($this->pipes, fn (string $p) => $p !== $pipe);

        return $this;
    }
}
