<?php

namespace PictaStudio\VenditioCore\Base\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use PictaStudio\VenditioCore\DataTypes\Decimal as DecimalDataType;

class Decimal implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): DecimalDataType
    {
        $value = preg_replace('/[^0-9]/', '', $value);

        Validator::make([
            $key => $value,
        ], [
            $key => 'nullable|numeric',
        ])->validate();

        return new DecimalDataType(
            (int) $value,
            // $model->priceable->unit_quantity ?? $model->unit_quantity ?? 1,
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [
            $key => $value,
        ];
    }
}
