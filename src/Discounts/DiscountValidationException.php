<?php

namespace PictaStudio\Venditio\Discounts;

use Illuminate\Validation\ValidationException;

class DiscountValidationException
{
    public static function invalidCartTotalDiscountCode(string $discountCode): ValidationException
    {
        return ValidationException::withMessages([
            'discount_code' => [
                sprintf(
                    'The discount code [%s] is invalid or not eligible for cart total discounts.',
                    $discountCode
                ),
            ],
        ]);
    }
}
