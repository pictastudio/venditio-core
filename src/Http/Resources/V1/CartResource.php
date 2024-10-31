<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Resources\Traits\CanTransformAttributes;
use PictaStudio\VenditioCore\Http\Resources\Traits\HasAttributesToExclude;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class CartResource extends JsonResource
{
    use HasAttributesToExclude;
    use CanTransformAttributes;

    public function toArray(Request $request): array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
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
        return [
            'lines' => CartLineResource::collection($this->whenLoaded('lines')),
        ];
    }

    protected function transformAttributes(): array
    {
        return [
            //
        ];
    }
}
