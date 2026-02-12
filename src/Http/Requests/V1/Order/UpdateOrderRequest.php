<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use PictaStudio\Venditio\Validations\Contracts\OrderValidationRules;

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
