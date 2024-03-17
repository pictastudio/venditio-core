<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Http\Resources\Traits\HasAttributesToExclude;

class AddressResource extends JsonResource
{
    use HasAttributesToExclude;

    public function toArray($request)
    {
        $attributes = Arr::except(
            parent::toArray($request),
            array_merge(
                $this->getDefaultAttributesToExclude(),
                [
                    'addressable_type',
                    'addressable_id',
                ]
            )
        );

        return array_merge(
            $attributes,
            $this->getRelationshipsToInclude(),
        );
    }

    protected function getRelationshipsToInclude(): array
    {
        return [];
    }
}
