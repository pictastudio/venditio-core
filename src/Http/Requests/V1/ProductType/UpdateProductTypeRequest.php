<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\ProductType;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\ProductTypeValidationRules;

class UpdateProductTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product-type:update') ?? true;
    }

    public function rules(ProductTypeValidationRules $productTypeValidationRules): array
    {
        return $productTypeValidationRules->getUpdateValidationRules();
    }
}
