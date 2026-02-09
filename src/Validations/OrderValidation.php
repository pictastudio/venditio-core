<?php

namespace PictaStudio\VenditioCore\Validations;

use PictaStudio\VenditioCore\Validations\Contracts\OrderValidationRules;

class OrderValidation implements OrderValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'cart_id' => 'required|integer|exists:carts,id',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [];
    }
}
