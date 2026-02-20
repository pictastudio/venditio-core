<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Http\Requests\V1\Concerns\InteractsWithTranslatableInput;
use PictaStudio\Venditio\Validations\Contracts\ProductValidationRules;

class UpdateProductRequest extends FormRequest
{
    use InteractsWithTranslatableInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(ProductValidationRules $productValidationRules): array
    {
        return $productValidationRules->getUpdateValidationRules();
    }

    protected function prepareForValidation(): void
    {
        $this->prepareTranslatableInput();
        $this->prepareTranslatedSlugInput();
    }
}
