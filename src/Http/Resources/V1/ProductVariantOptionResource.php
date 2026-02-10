<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Resources\Traits\CanTransformAttributes;
use PictaStudio\VenditioCore\Http\Resources\Traits\HasAttributesToExclude;
use Illuminate\Support\Facades\URL;

class ProductVariantOptionResource extends JsonResource
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
            //
        ];
    }

    protected function transformAttributes(): array
    {
        return [
            'image' => function (?string $image) {
                if (blank($image)) {
                    return null;
                }

                return URL::isValidUrl($image) ? $image : asset('storage/' . $image);
            },
        ];
    }
}
