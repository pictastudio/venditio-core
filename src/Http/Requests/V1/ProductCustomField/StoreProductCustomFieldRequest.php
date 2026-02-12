<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductCustomField;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class StoreProductCustomFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_type_id' => ['required', 'integer', Rule::exists($this->tableFor('product_type'), 'id')],
            'name' => 'required|string|max:255',
            'required' => 'sometimes|boolean',
            'sort_order' => 'required|integer|min:0',
            'type' => 'required|string|max:255',
            'options' => 'nullable|array',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
