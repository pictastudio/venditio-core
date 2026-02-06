<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\ProductCategoryValidationRules;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product-category:update') ?? true;
    }

    public function rules(ProductCategoryValidationRules $productCategoryValidationRules): array
    {
        return $productCategoryValidationRules->getUpdateValidationRules();
    }
}
