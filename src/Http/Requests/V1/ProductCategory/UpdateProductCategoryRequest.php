<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product-category:update');
    }

    public function rules(): array
    {
        return [];
    }
}
