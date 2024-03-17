<?php

namespace PictaStudio\VenditioCore\Validations;

use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;

class Cart implements CartValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'user_id' => 'nullable|integer|exists:users,id',
            'user_first_name' => 'nullable|string|max:255',
            'user_last_name' => 'nullable|string|max:255',
            'user_email' => 'nullable|email|max:255',
            'discount_ref' => 'nullable|string',
            'billing_address' => 'nullable|array',
            ...$this->getAddressValidationRules('billing_address'),
            'shipping_address' => 'nullable|array',
            ...$this->getAddressValidationRules('shipping_address'),
            'lines' => 'nullable|array',
            'lines.*.product_item_id' => 'nullable|integer|exists:product_items,id',
            'lines.*.qty' => 'nullable|integer|min:1',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            // 'user_id' => 'nullable|integer|exists:users,id',
            'user_first_name' => 'nullable|string|max:255',
            'user_last_name' => 'nullable|string|max:255',
            'user_email' => 'nullable|email|max:255',
            'discount_ref' => 'nullable|string',
            'billing_address' => 'nullable|array',
            ...$this->getAddressValidationRules('billing_address'),
            'shipping_address' => 'nullable|array',
            ...$this->getAddressValidationRules('shipping_address'),
            'lines' => 'nullable|array',
            'lines.*.product_item_id' => 'nullable|integer|exists:product_items,id',
            'lines.*.qty' => 'nullable|integer|min:1',
        ];
    }

    private function getAddressValidationRules(string $key): array
    {
        return [
            $key . '.first_name' => 'nullable|string|max:255',
            $key . '.last_name' => 'nullable|string|max:255',
            $key . '.email' => 'nullable|email|max:255',
            $key . '.sex' => 'nullable|string|in:m,f',
            $key . '.phone' => 'nullable|string|max:50',
            $key . '.vat_number' => 'nullable|string|max:25',
            $key . '.fiscal_code' => 'nullable|string|max:25',
            $key . '.company_name' => 'nullable|string',
            $key . '.address_line_1' => 'nullable|string',
            $key . '.address_line_2' => 'nullable|string',
            $key . '.city' => 'nullable|string|max:100',
            $key . '.state' => 'nullable|string|max:10',
            $key . '.zip' => 'nullable|string|max:10',
            $key . '.birth_date' => 'nullable|date',
            $key . '.birth_place' => 'nullable|string|max:100',
            $key . '.notes' => 'nullable|string',
        ];
    }
}
