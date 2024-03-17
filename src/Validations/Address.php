<?php

namespace PictaStudio\VenditioCore\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\AddressValidationRules;

class Address implements AddressValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::enum(config('venditio-core.addresses.type_enum')),
            ],
            'default' => 'sometimes|boolean',
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
                'nullable',
                'string',
                Rule::enum(config('venditio-core.addresses.type_enum')),
            ],
            'default' => 'sometimes|boolean',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'sex' => 'nullable|string|in:m,f',
            'phone' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:25',
            'fiscal_code' => 'nullable|string|max:25',
            'company_name' => 'nullable|string',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:10',
            'zip' => 'nullable|string|max:10',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
