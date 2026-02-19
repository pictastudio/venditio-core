<?php

namespace PictaStudio\Venditio\Http\Requests\V1\PriceListPrice;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\PriceListPriceValidationRules;

class UpdatePriceListPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(PriceListPriceValidationRules $priceListPriceValidationRules): array
    {
        return $priceListPriceValidationRules->getUpdateValidationRules();
    }
}
