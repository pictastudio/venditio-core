<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Brand;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\BrandValidationRules;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(BrandValidationRules $brandValidationRules): array
    {
        return $brandValidationRules->getStoreValidationRules();
    }
}
