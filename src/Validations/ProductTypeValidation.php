<?php

namespace PictaStudio\Venditio\Validations;

use PictaStudio\Venditio\Validations\Contracts\ProductTypeValidationRules;

class ProductTypeValidation implements ProductTypeValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'active' => 'sometimes|boolean',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'active' => 'sometimes|boolean',
        ];
    }
}
