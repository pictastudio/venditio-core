<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\ProductCategoryValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class ProductCategoryValidation implements ProductCategoryValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists($this->tableFor('product_category'), 'id'),
            ],
            'name' => 'required|string|max:255',
            'active' => 'sometimes|boolean',
            'sort_order' => 'required|integer|min:0',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'parent_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists($this->tableFor('product_category'), 'id'),
            ],
            'name' => 'sometimes|string|max:255',
            'active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
