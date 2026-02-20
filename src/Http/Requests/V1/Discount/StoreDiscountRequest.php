<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Discount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rule;
use PictaStudio\Venditio\Enums\DiscountType;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class StoreDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discountable_type' => [
                'nullable',
                'string',
                'max:255',
                Rule::in(['product', 'product_category', 'product_type', 'brand', 'user']),
            ],
            'discountable_id' => [
                'nullable',
                'integer',
                'required_with:discountable_type',
                Rule::exists($this->tableFor($this->discountable_type), 'id'),
            ],
            'type' => ['required', Rule::enum(DiscountType::class)],
            'value' => ['required', 'numeric', 'min:0'],
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:discounts,code'],
            'active' => ['sometimes', 'boolean'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'uses' => ['sometimes', 'integer', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:0'],
            'apply_to_cart_total' => ['sometimes', 'boolean'],
            'apply_once_per_cart' => ['sometimes', 'boolean'],
            'max_uses_per_user' => ['nullable', 'integer', 'min:1'],
            'one_per_user' => ['sometimes', 'boolean'],
            'free_shipping' => ['sometimes', 'boolean'],
            'minimum_order_total' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['sometimes', 'integer'],
            'stop_after_propagation' => ['sometimes', 'boolean'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'starts_at' => Date::parse($this->starts_at),
        ]);
    }

    private function tableFor(?string $model): string
    {
        $resolvedModel = filled($model) ? $model : 'product';

        return (new (resolve_model($resolvedModel)))->getTable();
    }
}
