<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\ProductCategoryValidationRules;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product-category:create') ?? true;
    }

    public function rules(ProductCategoryValidationRules $productCategoryValidationRules): array
    {
        return $productCategoryValidationRules->getStoreValidationRules();
    }
}
