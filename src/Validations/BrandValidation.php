<?php

namespace PictaStudio\Venditio\Validations;

use PictaStudio\Venditio\Validations\Contracts\BrandValidationRules;

class BrandValidation implements BrandValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
