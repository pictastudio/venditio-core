<?php

namespace PictaStudio\Venditio\Http\Requests\V1\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Http\Requests\V1\Concerns\InteractsWithTranslatableInput;
use PictaStudio\Venditio\Validations\Contracts\ProductCategoryValidationRules;

class UpdateProductCategoryRequest extends FormRequest
{
    use InteractsWithTranslatableInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(ProductCategoryValidationRules $productCategoryValidationRules): array
    {
        return $productCategoryValidationRules->getUpdateValidationRules();
    }

    protected function prepareForValidation(): void
    {
        $this->prepareTranslatableInput();
        $this->prepareTranslatedSlugInput();
    }
}
