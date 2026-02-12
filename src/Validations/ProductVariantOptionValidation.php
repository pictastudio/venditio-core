<?php

namespace PictaStudio\Venditio\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\Venditio\Validations\Contracts\ProductVariantOptionValidationRules;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class ProductVariantOptionValidation implements ProductVariantOptionValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'product_variant_id' => [
                'required',
                'integer',
                Rule::exists($this->tableFor('product_variant'), 'id'),
            ],
            'name' => [
                'required',
                'string',
            ],
            'image' => 'sometimes|nullable|string',
            'hex_color' => 'sometimes|nullable|string|max:20',
            'sort_order' => 'required|integer|min:0',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'product_variant_id' => [
                'sometimes',
                'integer',
                Rule::exists($this->tableFor('product_variant'), 'id'),
            ],
            'name' => 'sometimes|string',
            'image' => 'sometimes|nullable|string',
            'hex_color' => 'sometimes|nullable|string|max:20',
            'sort_order' => 'sometimes|integer|min:0',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
