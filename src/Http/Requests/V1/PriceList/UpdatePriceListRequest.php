<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\PriceList;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\PriceListValidationRules;

class UpdatePriceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('price-list:update') ?? true;
    }

    public function rules(PriceListValidationRules $priceListValidationRules): array
    {
        return $priceListValidationRules->getUpdateValidationRules();
    }
}
