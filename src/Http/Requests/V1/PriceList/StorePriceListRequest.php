<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\PriceList;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\PriceListValidationRules;

class StorePriceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('price-list:create') ?? true;
    }

    public function rules(PriceListValidationRules $priceListValidationRules): array
    {
        return $priceListValidationRules->getStoreValidationRules();
    }
}
