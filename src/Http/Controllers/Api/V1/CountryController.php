<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\Country\{StoreCountryRequest, UpdateCountryRequest};
use PictaStudio\Venditio\Http\Resources\V1\GenericModelResource;
use PictaStudio\Venditio\Models\Country;

use function PictaStudio\Venditio\Helpers\Functions\query;

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
        $payload = $request->validated();
        $currencyIds = Arr::pull($payload, 'currency_ids');

        $country = query('country')->create($payload);

        if (is_array($currencyIds)) {
            $country->currencies()->sync($currencyIds);
        }

        return GenericModelResource::make($country);
    }

    public function show(Country $country): JsonResource
    {
        return GenericModelResource::make($country);
    }

    public function update(UpdateCountryRequest $request, Country $country): JsonResource
    {
        $payload = $request->validated();
        $currencyIds = Arr::pull($payload, 'currency_ids');

        $country->fill($payload);
        $country->save();

        if (is_array($currencyIds)) {
            $country->currencies()->sync($currencyIds);
        }

        return GenericModelResource::make($country->refresh());
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return response()->noContent();
    }
}
