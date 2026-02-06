<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product:create');
    }

    public function rules(): array
    {
        return [];
    }
}
