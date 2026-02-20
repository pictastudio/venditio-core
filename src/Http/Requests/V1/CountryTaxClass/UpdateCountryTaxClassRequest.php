<?php

namespace PictaStudio\Venditio\Http\Requests\V1\CountryTaxClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class UpdateCountryTaxClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['sometimes', 'integer', Rule::exists($this->tableFor('country'), 'id')],
            'tax_class_id' => ['sometimes', 'integer', Rule::exists($this->tableFor('tax_class'), 'id')],
            'rate' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
