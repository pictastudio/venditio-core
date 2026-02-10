<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Country\{StoreCountryRequest, UpdateCountryRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\GenericModelResource;
use PictaStudio\VenditioCore\Models\Country;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class CountryController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return GenericModelResource::collection(
            $this->applyBaseFilters(query('country'), request()->all(), 'country')
        );
    }

    public function store(StoreCountryRequest $request): JsonResource
    {
        $country = query('country')->create($request->validated());

        return GenericModelResource::make($country);
    }

    public function show(Country $country): JsonResource
    {
        return GenericModelResource::make($country);
    }

    public function update(UpdateCountryRequest $request, Country $country): JsonResource
    {
        $country->fill($request->validated());
        $country->save();

        return GenericModelResource::make($country->refresh());
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return response()->noContent();
    }
}
