<?php

namespace PictaStudio\VenditioCore\Http\Resources\Traits;

trait HasAttributesToExclude
{
    public function getDefaultAttributesToExclude(): array
    {
        if (!config('venditio-core.routes.api.include_timestamps')) {
            return [
                // 'created_at',
                'updated_at',
                'deleted_at',
            ];
        }

        return [];
    }
}
