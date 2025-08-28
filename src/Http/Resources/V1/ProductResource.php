<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use PictaStudio\VenditioCore\Http\Resources\Traits\{CanTransformAttributes, HasAttributesToExclude};

class ProductResource extends JsonResource
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
            'images' => fn (?array $images) => (
                collect($images)
                    ->map(fn (array $image) => [
                        'alt' => $image['alt'],
                        'src' => URL::isValidUrl($image['src']) ? $image['src'] : asset('storage/' . $image['src']),
                    ])
                    ->toArray()
            ),
            'files' => fn (?array $files) => (
                collect($files)
                    ->map(fn (array $file) => [
                        'name' => $file['name'],
                        'src' => URL::isValidUrl($file['src']) ? $file['src'] : asset('storage/' . $file['src']),
                    ])
                    ->toArray()
            ),
        ];
    }
}
