<?php

namespace PictaStudio\Venditio\Dto\Contracts;

use Illuminate\Support\Collection;

interface Dto
{
    public static function fromArray(array $data): static;

    public function fill(array $data): static;

    public function toArray(): array;

    public function toCollection(): Collection;
}
