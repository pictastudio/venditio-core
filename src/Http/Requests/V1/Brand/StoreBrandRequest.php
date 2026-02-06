<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Brand;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\BrandValidationRules;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('brand:create') ?? true;
    }

    public function rules(BrandValidationRules $brandValidationRules): array
    {
        return $brandValidationRules->getStoreValidationRules();
    }
}
