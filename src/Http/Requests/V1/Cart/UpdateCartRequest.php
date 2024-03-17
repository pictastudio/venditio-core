<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Cart;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cart:update');
    }

    public function rules(CartValidationRules $cartValidationRules): array
    {
        return $cartValidationRules->getUpdateValidationRules();
    }
}
