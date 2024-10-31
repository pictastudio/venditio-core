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

    private function mutateAttributeBasedOnCast(string $key, mixed $value): mixed
    {
        /** @var Model $model */
        $model = $this->resource;

        if (!$model->hasCast($key)) {
            return $value;
        }

        $cast = $model->getCasts()[$key];

        if (str_contains($cast, 'decimal')) {
            return (float) $value;
        }

        if (in_array($cast, ['int', 'integer'])) {
            return (int) $value;
        }

        if (in_array($cast, ['bool', 'boolean'])) {
            return (bool) $value;
        }

        return $value;
    }
}
