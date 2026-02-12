<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\PriceListPrice;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\PriceListPriceValidationRules;

class StorePriceListPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('price-list-price:create') ?? true;
    }

    public function rules(PriceListPriceValidationRules $priceListPriceValidationRules): array
    {
        return $priceListPriceValidationRules->getStoreValidationRules();
    }
}
