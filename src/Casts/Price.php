<?php

namespace PictaStudio\VenditioCore\Base\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use PictaStudio\VenditioCore\DataTypes\Price as PriceDataType;
use PictaStudio\VenditioCore\Models\Currency;

class Price implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): PriceDataType
    {
        $currency = $model->currency ?: Currency::getDefault();

        $value = preg_replace('/[^0-9]/', '', $value);

        Validator::make([
            $key => $value,
        ], [
            $key => 'nullable|numeric',
        ])->validate();

        return new PriceDataType(
            (int) $value,
            $currency,
            $model->priceable->unit_quantity ?? $model->unit_quantity ?? 1,
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [
            $key => $value,
        ];
    }
}
