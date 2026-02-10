<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $countryId = $this->route('country')?->getKey();

        return [
            'name' => 'sometimes|string|max:255',
            'iso_2' => ['sometimes', 'string', 'size:2', Rule::unique('countries', 'iso_2')->ignore($countryId)],
            'iso_3' => ['sometimes', 'string', 'size:3', Rule::unique('countries', 'iso_3')->ignore($countryId)],
            'phone_code' => 'sometimes|string|max:20',
            'currency_code' => 'sometimes|string|size:3',
            'flag_emoji' => 'sometimes|string|max:50',
            'capital' => 'sometimes|string|max:150',
            'native' => 'nullable|string|max:150',
        ];
    }
}
