<?php

namespace PictaStudio\VenditioCore\Http\Resources\Traits;

trait HasAttributesToExclude
{
    protected function getAttributesToExclude(): array
    {
        $attributes = $this->exclude();

        if (!config('venditio-core.routes.api.include_timestamps')) {
            $attributes = array_merge($attributes, [
                // 'created_at',
                'updated_at',
                'deleted_at',
            ]);
        }

        return $attributes;
    }

    protected function exclude(): array
    {
        return [];
    }
}
