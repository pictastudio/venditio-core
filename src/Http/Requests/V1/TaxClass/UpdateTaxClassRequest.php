<?php

namespace PictaStudio\Venditio\Http\Requests\V1\TaxClass;

use Illuminate\Foundation\Http\FormRequest;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class UpdateTaxClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'is_default' => 'sometimes|boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->boolean('is_default') !== true) {
                return;
            }

            $taxClass = $this->route('tax_class');

            $hasOtherDefault = resolve_model('tax_class')::where('is_default', true)
                ->when($taxClass, fn ($q) => $q->whereKeyNot($taxClass->getKey()))
                ->exists();

            if ($hasOtherDefault) {
                $validator->errors()->add(
                    'is_default',
                    'Another tax class is already set as default. Only one default is allowed.'
                );
            }
        });
    }
}
