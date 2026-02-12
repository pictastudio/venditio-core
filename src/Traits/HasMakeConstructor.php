<?php

namespace PictaStudio\Venditio\Traits;

trait HasMakeConstructor
{
    public static function make(array $parameters = []): static
    {
        return app(static::class, $parameters);
    }
}
