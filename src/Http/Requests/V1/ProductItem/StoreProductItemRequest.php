<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\ProductItem;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductItemRequest extends FormRequest
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
