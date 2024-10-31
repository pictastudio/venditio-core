<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Validations;

use BackedEnum;
use PictaStudio\VenditioCore\Packages\Simple\Validations\CartValidation as CartValidationSimple;
use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;

class CartValidation extends CartValidationSimple implements CartValidationRules
{
    public function getStoreValidationRules(): array
    {
        $rules = parent::getStoreValidationRules();

        $rules['lines.*.product_id'] = 'missing';
        $rules['lines.*.product_item_id'] = 'nullable|integer|exists:product_items,id';

        return $rules;
    }

    public function getUpdateValidationRules(): array
    {
        $rules = parent::getUpdateValidationRules();

        $rules['lines.*.product_id'] = 'missing';
        $rules['lines.*.product_item_id'] = 'nullable|integer|exists:product_items,id';

        return $rules;
    }
}
