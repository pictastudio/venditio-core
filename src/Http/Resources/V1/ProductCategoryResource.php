<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Resources\Traits\{CanTransformAttributes, HasAttributesToExclude};

class ProductCategoryResource extends JsonResource
{
    use CanTransformAttributes;
    use HasAttributesToExclude;

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
        return [
            // 'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }

    protected function transformAttributes(): array
    {
        return [
            //
        ];
    }
}
