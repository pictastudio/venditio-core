<?php

namespace PictaStudio\Venditio\Http\Requests\V1\CountryTaxClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class StoreCountryTaxClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['required', 'integer', Rule::exists($this->tableFor('country'), 'id')],
            'tax_class_id' => ['required', 'integer', Rule::exists($this->tableFor('tax_class'), 'id')],
            'rate' => ['required', 'numeric', 'min:0'],
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
