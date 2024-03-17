<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product-category:create');
    }

    public function rules(): array
    {
        return [];
    }
}
