<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Resources\Traits\CanTransformAttributes;
use PictaStudio\VenditioCore\Http\Resources\Traits\HasAttributesToExclude;
use Illuminate\Http\Request;

class AddressResource extends JsonResource
{
    use HasAttributesToExclude;
    use CanTransformAttributes;

    public function toArray(Request $request)
    {
        return $this->applyAttributesTransformation(
            collect(parent::toArray($request))
                ->except($this->getAttributesToExclude())
                ->map(fn (mixed $value, string $key) => (
                    $this->mutateAttributeBasedOnCast($key, $value)
                ))
                ->merge($this->getRelationshipsToInclude())
                ->toArray()
        );
    }

    protected function getRelationshipsToInclude(): array
    {
        return [];
    }

    protected function exclude(): array
    {
        return [
            'addressable_type',
            'addressable_id',
        ];
    }

    protected function transformAttributes(): array
    {
        return [
            //
        ];
    }
}
