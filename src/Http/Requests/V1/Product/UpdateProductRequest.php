<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\ProductValidationRules;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product:update') ?? true;
    }

    public function rules(ProductValidationRules $productValidationRules): array
    {
        return $productValidationRules->getUpdateValidationRules();
    }
}
