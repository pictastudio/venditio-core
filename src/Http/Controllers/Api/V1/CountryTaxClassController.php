<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\CountryTaxClass\{StoreCountryTaxClassRequest, UpdateCountryTaxClassRequest};
use PictaStudio\Venditio\Http\Resources\V1\GenericModelResource;
use PictaStudio\Venditio\Models\CountryTaxClass;

use function PictaStudio\Venditio\Helpers\Functions\query;

class CountryTaxClassController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', CountryTaxClass::class);

        return GenericModelResource::collection(
            $this->applyBaseFilters(query('country_tax_class'), request()->all(), 'country_tax_class')
        );
    }

    public function store(StoreCountryTaxClassRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', CountryTaxClass::class);

        $countryTaxClass = query('country_tax_class')->create($request->validated());

        return GenericModelResource::make($countryTaxClass);
    }

    public function show(CountryTaxClass $countryTaxClass): JsonResource
    {
        $this->authorizeIfConfigured('view', $countryTaxClass);

        return GenericModelResource::make($countryTaxClass);
    }

    public function update(UpdateCountryTaxClassRequest $request, CountryTaxClass $countryTaxClass): JsonResource
    {
        $this->authorizeIfConfigured('update', $countryTaxClass);

        $countryTaxClass->fill($request->validated());
        $countryTaxClass->save();

        return GenericModelResource::make($countryTaxClass->refresh());
    }

    public function destroy(CountryTaxClass $countryTaxClass)
    {
        $this->authorizeIfConfigured('delete', $countryTaxClass);

        $countryTaxClass->delete();

        return response()->noContent();
    }
}
