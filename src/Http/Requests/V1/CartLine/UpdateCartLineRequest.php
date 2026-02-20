<?php

namespace PictaStudio\Venditio\Http\Requests\V1\CartLine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class UpdateCartLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_id' => ['sometimes', 'integer', Rule::exists($this->tableFor('cart'), 'id')],
            'product_id' => ['sometimes', 'integer', Rule::exists($this->tableFor('product'), 'id')],
            'discount_id' => ['nullable', 'integer', Rule::exists($this->tableFor('discount'), 'id')],
            'product_name' => ['sometimes', 'string', 'max:255'],
            'product_sku' => ['sometimes', 'string', 'max:255'],
            'discount_code' => ['nullable', 'string', 'max:30'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'unit_price' => ['sometimes', 'numeric', 'min:0'],
            'unit_discount' => ['sometimes', 'numeric', 'min:0'],
            'unit_final_price' => ['sometimes', 'numeric', 'min:0'],
            'unit_final_price_tax' => ['sometimes', 'numeric', 'min:0'],
            'unit_final_price_taxable' => ['sometimes', 'numeric', 'min:0'],
            'qty' => ['sometimes', 'integer', 'min:1'],
            'total_final_price' => ['sometimes', 'numeric', 'min:0'],
            'tax_rate' => ['sometimes', 'numeric', 'min:0'],
            'product_data' => ['sometimes', 'array'],
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
