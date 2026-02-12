<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\ProductType;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\ProductTypeValidationRules;

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
}
