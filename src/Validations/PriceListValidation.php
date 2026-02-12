<?php

namespace PictaStudio\VenditioCore\Validations;

use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Validations\Contracts\PriceListValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class PriceListValidation implements PriceListValidationRules
{
    public function getStoreValidationRules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($this->tableFor('price_list'), 'name')
                    ->withoutTrashed(),
            ],
            'code' => ['nullable', 'string', 'max:255', Rule::unique($this->tableFor('price_list'), 'code')->withoutTrashed()],
            'active' => 'sometimes|boolean',
            'description' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        $priceList = request()->route('price_list');
        $priceListId = $priceList?->getKey();

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique($this->tableFor('price_list'), 'name')
                    ->withoutTrashed()
                    ->ignore($priceListId),
            ],
            'code' => ['nullable', 'string', 'max:255', Rule::unique($this->tableFor('price_list'), 'code')->withoutTrashed()->ignore($priceListId)],
            'active' => 'sometimes|boolean',
            'description' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ];
    }

    private function tableFor(string $model): string
    {
        return (new (resolve_model($model)))->getTable();
    }
}
