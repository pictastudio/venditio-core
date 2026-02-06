<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\ProductVariantOption;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\ProductVariantOptionValidationRules;

class StoreProductVariantOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product-variant-option:create') ?? true;
    }

    public function rules(ProductVariantOptionValidationRules $productVariantOptionValidationRules): array
    {
        return $productVariantOptionValidationRules->getStoreValidationRules();
    }
}
