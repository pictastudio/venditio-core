<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ShippingStatus;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
