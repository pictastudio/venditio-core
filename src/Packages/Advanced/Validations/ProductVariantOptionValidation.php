<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\ProductVariantOptionValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

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
            'value' => [
                'required',
                function (string $attribute, mixed $value, callable $fail) {
                    if (!is_string($value) && !is_array($value)) {
                        $fail('The value field must be a string or an array.');
                    }
                },
            ],
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
            'value' => [
                'sometimes',
                function (string $attribute, mixed $value, callable $fail) {
                    if (!is_string($value) && !is_array($value)) {
                        $fail('The value field must be a string or an array.');
                    }
                },
            ],
            'sort_order' => 'sometimes|integer|min:0',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
