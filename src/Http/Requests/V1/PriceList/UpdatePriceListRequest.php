<?php

namespace PictaStudio\Venditio\Http\Requests\V1\PriceList;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\PriceListValidationRules;

class UpdatePriceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(PriceListValidationRules $priceListValidationRules): array
    {
        return $priceListValidationRules->getUpdateValidationRules();
    }
}
