<?php

namespace PictaStudio\Venditio\Http\Resources\V1;

use Illuminate\Http\Request;

class ProductCollectionProductResource extends ProductResource
{
    public function toArray(Request $request)
    {
        return [
            ...parent::toArray($request),
            'sort_order' => (int) ($this->resource->pivot?->sort_order ?? 0),
        ];
    }
}
