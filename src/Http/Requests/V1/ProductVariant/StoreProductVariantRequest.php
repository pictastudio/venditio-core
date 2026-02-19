<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\ProductVariantValidationRules;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(ProductVariantValidationRules $productVariantValidationRules): array
    {
        return $productVariantValidationRules->getStoreValidationRules();
    }
}
