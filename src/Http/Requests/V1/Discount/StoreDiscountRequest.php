<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Discount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PictaStudio\Venditio\Enums\DiscountType;

use function PictaStudio\Venditio\Helpers\Functions\{query, resolve_model};

class StoreDiscountRequest extends FormRequest
{
    private const PRODUCT_PRIORITY = 2147483647;

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
                'required_with:discountable_id',
                Rule::in(['product', 'product_category', 'product_collection', 'product_type', 'brand', 'user']),
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
            'code' => [
                'nullable',
                Rule::requiredIf(fn () => !$this->hasDiscountableTarget()),
                'string',
                'max:50',
                Rule::unique($this->discountsTable(), 'code'),
            ],
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
            'first_purchase_only' => ['sometimes', 'boolean'],
            'minimum_order_total' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['sometimes', 'integer'],
            'standalone' => ['sometimes', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $payload = [
            'starts_at' => Date::parse($this->starts_at),
        ];

        if ($this->shouldGenerateAutomaticCode()) {
            $payload['code'] = $this->generateAutomaticCode();
        }

        if ($this->input('discountable_type') === 'product') {
            $payload['priority'] = self::PRODUCT_PRIORITY;
        }

        $this->merge($payload);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (!$this->isFixedPriceDiscount()) {
                return;
            }

            if ($this->input('discountable_type') === 'product' && filled($this->input('discountable_id'))) {
                return;
            }

            $validator->errors()->add(
                'type',
                'The fixed_price discount type is only available for product discountable targets.'
            );
        });
    }

    private function tableFor(?string $model): string
    {
        $resolvedModel = filled($model) ? $model : 'product';

        return (new (resolve_model($resolvedModel)))->getTable();
    }

    private function discountsTable(): string
    {
        return (new (resolve_model('discount')))->getTable();
    }

    private function hasDiscountableTarget(): bool
    {
        return filled($this->input('discountable_type'))
            && filled($this->input('discountable_id'));
    }

    private function shouldGenerateAutomaticCode(): bool
    {
        return $this->hasDiscountableTarget() && blank($this->input('code'));
    }

    private function isFixedPriceDiscount(): bool
    {
        $type = $this->input('type');

        return $type === DiscountType::FixedPrice->value
            || $type === DiscountType::FixedPrice;
    }

    private function generateAutomaticCode(): string
    {
        do {
            $code = 'AUTO-' . mb_strtoupper(Str::random(12));
        } while (query('discount')->where('code', $code)->exists());

        return $code;
    }
}
