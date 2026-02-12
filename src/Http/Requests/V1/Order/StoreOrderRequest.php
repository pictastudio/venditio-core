<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\OrderValidationRules;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('order:create') ?? true;
    }

    public function rules(OrderValidationRules $orderValidationRules): array
    {
        return $orderValidationRules->getStoreValidationRules();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()?->getKey(),
        ]);
    }
}
