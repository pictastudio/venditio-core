<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\ProductValidationRules;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('product:create') ?? true;
    }

    public function rules(ProductValidationRules $productValidationRules): array
    {
        return $productValidationRules->getStoreValidationRules();
    }
}
