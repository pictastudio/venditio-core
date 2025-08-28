<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Brand;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\BrandValidationRules;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('brand:update');
    }

    public function rules(BrandValidationRules $brandValidationRules): array
    {
        return $brandValidationRules->getUpdateValidationRules();
    }
}
