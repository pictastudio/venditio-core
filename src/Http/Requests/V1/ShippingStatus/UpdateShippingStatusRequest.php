<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ShippingStatus;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_code' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
        ];
    }
}
