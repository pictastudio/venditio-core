<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Cart;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\CartValidationRules;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(CartValidationRules $cartValidationRules): array
    {
        return $cartValidationRules->getUpdateValidationRules();
    }
}
