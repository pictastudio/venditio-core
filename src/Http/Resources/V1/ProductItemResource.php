<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Http\Resources\Traits\CanTransformAttributes;
use PictaStudio\VenditioCore\Http\Resources\Traits\HasAttributesToExclude;

class ProductItemResource extends JsonResource
{
    use CanTransformAttributes;
    use HasAttributesToExclude;

    public function toArray($request)
    {
        $attributes = Arr::except(
            parent::toArray($request),
            $this->getDefaultAttributesToExclude()
        );

        return $this->applyAttributesTransformation(
            array_merge(
                $attributes,
                $this->getRelationshipsToInclude(),
            )
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
            'images' => fn (?array $images) => (
                collect($images)
                    ->map(fn (array $image) => [
                        'alt' => $image['alt'],
                        'img' => asset('storage/' . $image['img']),
                    ])
                    ->toArray()
            ),
        ];
    }
}
