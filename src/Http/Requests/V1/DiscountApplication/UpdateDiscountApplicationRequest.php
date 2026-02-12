<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\DiscountApplication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class UpdateDiscountApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount_id' => ['sometimes', 'integer', Rule::exists($this->tableFor('discount'), 'id')],
            'discountable_type' => 'nullable|string|max:255',
            'discountable_id' => 'nullable|integer|required_with:discountable_type',
            'user_id' => ['nullable', 'integer', Rule::exists($this->tableFor('user'), 'id')],
            'cart_id' => ['nullable', 'integer', Rule::exists($this->tableFor('cart'), 'id')],
            'order_id' => ['nullable', 'integer', Rule::exists($this->tableFor('order'), 'id')],
            'order_line_id' => ['nullable', 'integer', Rule::exists($this->tableFor('order_line'), 'id')],
            'qty' => 'sometimes|integer|min:1',
            'amount' => 'sometimes|numeric|min:0',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
