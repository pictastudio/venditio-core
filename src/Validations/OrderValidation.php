<?php

namespace PictaStudio\VenditioCore\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\OrderValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class OrderValidation implements OrderValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'cart_id' => [
                'required',
                'integer',
                Rule::exists($this->tableFor('cart'), 'id'),
            ],
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists($this->tableFor('user'), 'id'),
            ],
            'shipping_status_id' => [
                'nullable',
                'integer',
                Rule::exists($this->tableFor('shipping_status'), 'id'),
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::enum(config('venditio-core.order.status_enum')),
            ],
            'tracking_code' => 'nullable|string|max:150',
            'tracking_link' => 'nullable|string|max:2048',
            'last_tracked_at' => 'nullable|date',
            'courier_code' => 'nullable|string|max:50',
            'sub_total_taxable' => 'sometimes|numeric|min:0',
            'sub_total_tax' => 'sometimes|numeric|min:0',
            'sub_total' => 'sometimes|numeric|min:0',
            'shipping_fee' => 'sometimes|numeric|min:0',
            'payment_fee' => 'sometimes|numeric|min:0',
            'discount_code' => 'nullable|string|max:255',
            'discount_amount' => 'sometimes|numeric|min:0',
            'total_final' => 'sometimes|numeric|min:0',
            'user_first_name' => 'sometimes|string|max:255',
            'user_last_name' => 'sometimes|string|max:255',
            'user_email' => 'sometimes|email|max:255',
            'addresses' => 'sometimes|array',
            'customer_notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'approved_at' => 'nullable|date',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
