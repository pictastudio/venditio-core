<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product:update');
    }

    public function rules(): array
    {
        return [];
    }
}
