<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

interface Dto
{
    /**
     * This method should return a new instance of the Dto with the initial data (mostly null and empty arrays)
     * Then the data can be set using the setters or the `fromArray` method
     */
    public static function bindIntoContainer(): static;

    public static function fromArray(array $data): static;
}
