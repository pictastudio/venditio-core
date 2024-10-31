<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Dto
{
    public static function fromArray(array $data): static;

    public function fill(array $data): static;

    public function toArray(): array;

    public function toCollection(): Collection;
}
