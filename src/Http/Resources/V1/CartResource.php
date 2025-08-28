<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use PictaStudio\VenditioCore\Http\Resources\Traits\{CanTransformAttributes, HasAttributesToExclude};

class CartResource extends JsonResource
{
    use CanTransformAttributes;
    use HasAttributesToExclude;

    public function toArray(Request $request): array|Arrayable|JsonSerializable
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
