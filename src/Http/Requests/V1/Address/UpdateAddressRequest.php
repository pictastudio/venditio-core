<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Address;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\AddressValidationRules;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(AddressValidationRules $addressValidationRules): array
    {
        return $addressValidationRules->getUpdateValidationRules();
    }
}
