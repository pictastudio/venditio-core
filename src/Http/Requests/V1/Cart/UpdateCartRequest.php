<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Cart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cart:update');
    }

    public function rules(): array
    {
        return [
            // 'user_id' => 'nullable|integer|exists:users,id',
            'user_first_name' => 'required|string|max:255',
            'user_last_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'discount_ref' => 'nullable|string',
            'billing_address' => 'required|array',
            ...$this->getAddressValidationRules('billing_address'),
            'shipping_address' => 'required|array',
            ...$this->getAddressValidationRules('shipping_address'),
            'lines' => 'required|array',
            'lines.*.product_item_id' => 'required|integer|exists:product_items,id',
            'lines.*.qty' => 'required|integer|min:1',
        ];
    }

    private function getAddressValidationRules(string $key): array
    {
        return [
            $key . '.first_name' => 'required|string|max:255',
            $key . '.last_name' => 'required|string|max:255',
            $key . '.email' => 'required|email|max:255',
            $key . '.sex' => 'required|string|in:m,f',
            $key . '.phone' => 'required|string|max:50',
            $key . '.vat_number' => 'nullable|string|max:25',
            $key . '.fiscal_code' => 'required|string|max:25',
            $key . '.company_name' => 'nullable|string',
            $key . '.address_line_1' => 'required|string',
            $key . '.address_line_2' => 'nullable|string',
            $key . '.city' => 'required|string|max:100',
            $key . '.state' => 'required|string|max:10',
            $key . '.zip' => 'required|string|max:10',
            $key . '.birth_date' => 'nullable|date',
            $key . '.birth_place' => 'nullable|string|max:100',
            $key . '.notes' => 'nullable|string',
        ];
    }
}
