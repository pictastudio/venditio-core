<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class GenerateProductVariantsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variantTable = (new (resolve_model('product_variant')))->getTable();
        $optionTable = (new (resolve_model('product_variant_option')))->getTable();

        return [
            'variants' => 'required|array|min:1',
            'variants.*.variant_id' => [
                'required',
                'integer',
                Rule::exists($variantTable, 'id'),
            ],
            'variants.*.option_ids' => 'required|array|min:1',
            'variants.*.option_ids.*' => [
                'integer',
                Rule::exists($optionTable, 'id'),
            ],
        ];
    }
}
