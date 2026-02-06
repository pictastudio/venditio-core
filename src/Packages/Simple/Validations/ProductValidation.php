<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\ProductValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class ProductValidation implements ProductValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists($this->tableFor('product'), 'id'),
            ],
            'brand_id' => [
                'required',
                'integer',
                Rule::exists($this->tableFor('brand'), 'id'),
            ],
            'product_type_id' => [
                'nullable',
                'integer',
                Rule::exists($this->tableFor('product_type'), 'id'),
            ],
            'tax_class_id' => [
                'required',
                'integer',
                Rule::exists($this->tableFor('tax_class'), 'id'),
            ],
            'name' => 'required|string|max:255',
            'status' => [
                'required',
                'string',
                Rule::enum(config('venditio-core.product.status_enum')),
            ],
            'active' => 'sometimes|boolean',
            'new' => 'sometimes|boolean',
            'in_evidence' => 'sometimes|boolean',
            'sku' => 'nullable|string|max:255',
            'ean' => 'nullable|string|max:255',
            'visible_from' => 'nullable|date',
            'visible_until' => 'nullable|date|after_or_equal:visible_from',
            'description' => 'nullable|string',
            'description_short' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*.alt' => 'required_with:images|string|max:255',
            'images.*.src' => 'required_with:images|string|max:2048',
            'files' => 'nullable|array',
            'files.*.name' => 'required_with:files|string|max:255',
            'files.*.src' => 'required_with:files|string|max:2048',
            'measuring_unit' => [
                'nullable',
                'string',
                Rule::enum(config('venditio-core.product.measuring_unit_enum')),
            ],
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'metadata' => 'nullable|array',
            'category_ids' => 'nullable|array',
            'category_ids.*' => [
                'integer',
                Rule::exists($this->tableFor('product_category'), 'id'),
            ],
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'parent_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists($this->tableFor('product'), 'id'),
            ],
            'brand_id' => [
                'sometimes',
                'integer',
                Rule::exists($this->tableFor('brand'), 'id'),
            ],
            'product_type_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists($this->tableFor('product_type'), 'id'),
            ],
            'tax_class_id' => [
                'sometimes',
                'integer',
                Rule::exists($this->tableFor('tax_class'), 'id'),
            ],
            'name' => 'sometimes|string|max:255',
            'status' => [
                'sometimes',
                'string',
                Rule::enum(config('venditio-core.product.status_enum')),
            ],
            'active' => 'sometimes|boolean',
            'new' => 'sometimes|boolean',
            'in_evidence' => 'sometimes|boolean',
            'sku' => 'nullable|string|max:255',
            'ean' => 'nullable|string|max:255',
            'visible_from' => 'nullable|date',
            'visible_until' => 'nullable|date|after_or_equal:visible_from',
            'description' => 'nullable|string',
            'description_short' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*.alt' => 'required_with:images|string|max:255',
            'images.*.src' => 'required_with:images|string|max:2048',
            'files' => 'nullable|array',
            'files.*.name' => 'required_with:files|string|max:255',
            'files.*.src' => 'required_with:files|string|max:2048',
            'measuring_unit' => [
                'nullable',
                'string',
                Rule::enum(config('venditio-core.product.measuring_unit_enum')),
            ],
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'metadata' => 'nullable|array',
            'category_ids' => 'nullable|array',
            'category_ids.*' => [
                'integer',
                Rule::exists($this->tableFor('product_category'), 'id'),
            ],
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
