<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\ProductValidationRules;

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
