<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Address;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('address:update') ?? true;
    }

    public function rules(AddressValidationRules $addressValidationRules): array
    {
        return $addressValidationRules->getUpdateValidationRules();
    }
}
