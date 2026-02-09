<?php

namespace PictaStudio\VenditioCore\Validations;

use PictaStudio\VenditioCore\Validations\Contracts\ProductTypeValidationRules;

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
