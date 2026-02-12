<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductVariantOption;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\ProductVariantOptionValidationRules;

class UpdateProductVariantOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product-variant-option:update') ?? true;
    }

    public function rules(ProductVariantOptionValidationRules $productVariantOptionValidationRules): array
    {
        return $productVariantOptionValidationRules->getUpdateValidationRules();
    }
}
