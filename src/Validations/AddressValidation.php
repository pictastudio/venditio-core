<?php

namespace PictaStudio\VenditioCore\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;

class AddressValidation implements AddressValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::enum(config('venditio-core.addresses.type_enum')),
            ],
            'is_default' => 'sometimes|boolean',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sex' => 'required|string|in:m,f',
            'phone' => 'required|string|max:50',
            'vat_number' => 'nullable|string|max:25',
            'fiscal_code' => 'required|string|max:25',
            'company_name' => 'nullable|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:10',
            'zip' => 'required|string|max:10',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'type' => [
                'sometimes',
                'string',
                Rule::enum(config('venditio-core.addresses.type_enum')),
            ],
            'is_default' => 'sometimes|boolean',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'sex' => 'sometimes|string|in:m,f',
            'phone' => 'sometimes|string|max:50',
            'vat_number' => 'nullable|string|max:25',
            'fiscal_code' => 'sometimes|string|max:25',
            'company_name' => 'nullable|string',
            'address_line_1' => 'sometimes|string',
            'address_line_2' => 'nullable|string',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:10',
            'zip' => 'sometimes|string|max:10',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
