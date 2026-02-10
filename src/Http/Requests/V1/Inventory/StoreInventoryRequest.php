<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists($this->tableFor('product'), 'id')],
            'stock' => 'sometimes|integer|min:0',
            'stock_reserved' => 'sometimes|integer|min:0',
            'stock_min' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'price_includes_tax' => 'sometimes|boolean',
            'purchase_price' => 'nullable|numeric|min:0',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
