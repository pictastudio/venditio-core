<?php

namespace PictaStudio\VenditioCore\Http\Resources\Traits;

use Illuminate\Support\Arr;

trait CanTransformAttributes
{
    protected function transformAttributes(): array
    {
        return [];
    }

    public function applyAttributesTransformation(array $attributes): array
    {
        $transformedAttributes = $this->transformAttributes();

        if (empty($transformedAttributes)) {
            return $attributes;
        }

        foreach ($transformedAttributes as $key => $closure) {
            Arr::set($attributes, $key, $closure(Arr::get($attributes, $key)));
        }

        return $attributes;
    }
}
