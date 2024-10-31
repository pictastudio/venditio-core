<?php

namespace PictaStudio\VenditioCore\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidatesData
{
    public function validateData(array $data, array $rules): array
    {
        $validated = Validator::make($data, $rules);

        throw_if($validated->fails(), new ValidationException($validated));

        return $validated->safe()->all();
    }
}
