<?php

namespace PictaStudio\VenditioCore\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Http\Resources\Traits\HasAttributesToExclude;

class CartLineResource extends JsonResource
{
    use HasAttributesToExclude;

    public function toArray($request)
    {
        return Arr::except(
            parent::toArray($request),
            $this->getDefaultAttributesToExclude()
        );
    }
}
