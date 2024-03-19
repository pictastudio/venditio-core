<?php

namespace PictaStudio\VenditioCore\Validations;

use PictaStudio\VenditioCore\Validations\Contracts\CartLineValidationRules;

class CartLine implements CartLineValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'lines' => 'required|array',
            'lines.*.product_item_id' => 'required|integer|exists:product_items,id',
            'lines.*.qty' => 'required|integer|min:1',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'lines' => 'required|array',
            'lines.*.id' => 'required|integer|exists:cart_lines,id',
            // 'lines.*.product_item_id' => 'required|integer|exists:product_items,id',
            'lines.*.qty' => 'required|integer|min:1',
        ];
    }
}
