<?php

namespace PictaStudio\Venditio\Validations;

use PictaStudio\Venditio\Validations\Contracts\CartLineValidationRules;

class CartLineValidation implements CartLineValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'lines' => 'required|array',
            'lines.*.product_id' => 'required|integer|exists:products,id',
            'lines.*.qty' => 'required|integer|min:1',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'lines' => 'required|array',
            'lines.*.id' => 'required|integer|exists:cart_lines,id',
            // 'lines.*.product_id' => 'required|integer|exists:products,id',
            'lines.*.qty' => 'required|integer|min:1',
        ];
    }
}
