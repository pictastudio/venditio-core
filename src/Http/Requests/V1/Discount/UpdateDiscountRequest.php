<?php

namespace PictaStudio\VenditioCore\Http\Requests\V1\Discount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Enums\DiscountType;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $discountId = $this->route('discount')?->getKey();

        return [
            'discountable_type' => 'nullable|string|max:255',
            'discountable_id' => 'nullable|integer|required_with:discountable_type',
            'type' => ['sometimes', Rule::enum(DiscountType::class)],
            'value' => 'sometimes|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('discounts', 'code')->ignore($discountId)],
            'active' => 'sometimes|boolean',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'uses' => 'sometimes|integer|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'rules' => 'nullable|array',
            'priority' => 'sometimes|integer',
            'stop_after_propagation' => 'sometimes|boolean',
        ];
    }
}
