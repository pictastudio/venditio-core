<?php

namespace PictaStudio\Venditio\Http\Requests\V1\CartLine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class StoreCartLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_id' => ['required', 'integer', Rule::exists($this->tableFor('cart'), 'id')],
            'product_id' => ['required', 'integer', Rule::exists($this->tableFor('product'), 'id')],
            'discount_id' => ['nullable', 'integer', Rule::exists($this->tableFor('discount'), 'id')],
            'product_name' => ['required', 'string', 'max:255'],
            'product_sku' => ['required', 'string', 'max:255'],
            'discount_code' => ['nullable', 'string', 'max:30'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'unit_discount' => ['sometimes', 'numeric', 'min:0'],
            'unit_final_price' => ['required', 'numeric', 'min:0'],
            'unit_final_price_tax' => ['required', 'numeric', 'min:0'],
            'unit_final_price_taxable' => ['required', 'numeric', 'min:0'],
            'qty' => ['required', 'integer', 'min:1'],
            'total_final_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0'],
            'product_data' => ['required', 'array'],
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
