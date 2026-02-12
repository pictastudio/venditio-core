<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\ProductVariantValidationRules;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product-variant:update') ?? true;
    }

    public function rules(ProductVariantValidationRules $productVariantValidationRules): array
    {
        return $productVariantValidationRules->getUpdateValidationRules();
    }
}
