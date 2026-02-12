<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\PriceListPrice;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\PriceListPriceValidationRules;

class UpdatePriceListPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('price-list-price:update') ?? true;
    }

    public function rules(PriceListPriceValidationRules $priceListPriceValidationRules): array
    {
        return $priceListPriceValidationRules->getUpdateValidationRules();
    }
}
