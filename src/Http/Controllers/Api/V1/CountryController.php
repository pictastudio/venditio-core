<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Resources\V1\GenericModelResource;
use PictaStudio\Venditio\Models\Country;

use function PictaStudio\Venditio\Helpers\Functions\query;

class CountryController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', Country::class);

        return GenericModelResource::collection(
            $this->applyBaseFilters(query('country'), request()->all(), 'country')
        );
    }

    public function show(Country $country): JsonResource
    {
        $this->authorizeIfConfigured('view', $country);

        return GenericModelResource::make($country);
    }
}
