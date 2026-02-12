<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Address;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('address:create') ?? true;
    }

    public function rules(AddressValidationRules $addressValidationRules): array
    {
        return $addressValidationRules->getStoreValidationRules();
    }
}
