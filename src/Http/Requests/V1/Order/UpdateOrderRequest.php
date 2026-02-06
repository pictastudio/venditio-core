<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\VenditioCore\Validations\Contracts\OrderValidationRules;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('order:update') ?? true;
    }

    public function rules(OrderValidationRules $orderValidationRules): array
    {
        return $orderValidationRules->getUpdateValidationRules();
    }
}
