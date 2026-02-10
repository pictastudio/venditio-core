<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Discount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Enums\DiscountType;

class StoreDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discountable_type' => 'nullable|string|max:255',
            'discountable_id' => 'nullable|integer|required_with:discountable_type',
            'type' => ['required', Rule::enum(DiscountType::class)],
            'value' => 'required|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:discounts,code',
            'active' => 'sometimes|boolean',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'uses' => 'sometimes|integer|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'rules' => 'nullable|array',
            'priority' => 'sometimes|integer',
            'stop_after_propagation' => 'sometimes|boolean',
        ];
    }
}
