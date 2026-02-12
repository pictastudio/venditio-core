<?php

namespace PictaStudio\Venditio\Http\Requests\V1\TaxClass;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
