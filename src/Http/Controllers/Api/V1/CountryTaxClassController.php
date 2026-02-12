<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\CountryTaxClass\{StoreCountryTaxClassRequest, UpdateCountryTaxClassRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\GenericModelResource;
use PictaStudio\VenditioCore\Models\CountryTaxClass;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class CountryTaxClassController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return GenericModelResource::collection(
            $this->applyBaseFilters(query('country_tax_class'), request()->all(), 'country_tax_class')
        );
    }

    public function store(StoreCountryTaxClassRequest $request): JsonResource
    {
        $countryTaxClass = query('country_tax_class')->create($request->validated());

        return GenericModelResource::make($countryTaxClass);
    }

    public function show(CountryTaxClass $countryTaxClass): JsonResource
    {
        return GenericModelResource::make($countryTaxClass);
    }

    public function update(UpdateCountryTaxClassRequest $request, CountryTaxClass $countryTaxClass): JsonResource
    {
        $countryTaxClass->fill($request->validated());
        $countryTaxClass->save();

        return GenericModelResource::make($countryTaxClass->refresh());
    }

    public function destroy(CountryTaxClass $countryTaxClass)
    {
        $countryTaxClass->delete();

        return response()->noContent();
    }
}
