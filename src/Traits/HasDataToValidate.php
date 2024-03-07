<?php

namespace PictaStudio\VenditioCore\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

trait HasDataToValidate
{
    public function validateData(array $data, array $rules): JsonResponse|array
    {
        $validated = Validator::make($data, $rules);

        if ($validated->errors()->isNotEmpty()) {
            return response()->json([
                'errors' => $validated->errors()->toArray(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $validated->safe()->all();
    }
}
