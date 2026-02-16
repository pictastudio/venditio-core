<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductType;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Models\Scopes\Active;
use PictaStudio\Venditio\Validations\Contracts\ProductTypeValidationRules;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class StoreProductTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product-type:create') ?? true;
    }

    public function rules(ProductTypeValidationRules $productTypeValidationRules): array
    {
        return $productTypeValidationRules->getStoreValidationRules();
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->boolean('is_default') !== true) {
                return;
            }

            $hasOtherDefault = resolve_model('product_type')::withoutGlobalScope(Active::class)
                ->where('is_default', true)
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
