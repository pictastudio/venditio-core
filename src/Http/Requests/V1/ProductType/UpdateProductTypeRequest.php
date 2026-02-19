<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductType;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Models\Scopes\Active;
use PictaStudio\Venditio\Validations\Contracts\ProductTypeValidationRules;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class UpdateProductTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(ProductTypeValidationRules $productTypeValidationRules): array
    {
        return $productTypeValidationRules->getUpdateValidationRules();
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->boolean('is_default') !== true) {
                return;
            }

            $productType = $this->route('product_type');

            $hasOtherDefault = resolve_model('product_type')::withoutGlobalScope(Active::class)
                ->where('is_default', true)
                ->when($productType, fn ($q) => $q->whereKeyNot($productType->getKey()))
                ->exists();

            if ($hasOtherDefault) {
                $validator->errors()->add(
                    'is_default',
                    'Another product type is already set as default. Only one default is allowed.'
                );
            }
        });
    }
}
