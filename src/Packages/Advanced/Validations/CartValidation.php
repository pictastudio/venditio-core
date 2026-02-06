<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Validations;

use PictaStudio\VenditioCore\Packages\Simple\Validations\CartValidation as CartValidationSimple;
use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;

class CartValidation extends CartValidationSimple implements CartValidationRules
{
    public function getStoreValidationRules(): array
    {
        return parent::getStoreValidationRules();
    }

    public function getUpdateValidationRules(): array
    {
        return parent::getUpdateValidationRules();
    }
}
