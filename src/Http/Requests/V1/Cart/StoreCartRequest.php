<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Cart;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\CartValidationRules;

class StoreCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cart:create');
    }

    public function rules(CartValidationRules $cartValidationRules): array
    {
        return $cartValidationRules->getStoreValidationRules();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()?->getKey(),
        ]);
    }
}
