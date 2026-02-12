<?php

namespace PictaStudio\Venditio\Http\Resources\Traits;

trait HasAttributesToExclude
{
    protected function getAttributesToExclude(): array
    {
        $attributes = $this->exclude();

        if (!config('venditio.routes.api.include_timestamps')) {
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
