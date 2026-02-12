<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\Currency\{StoreCurrencyRequest, UpdateCurrencyRequest};
use PictaStudio\Venditio\Http\Resources\V1\GenericModelResource;
use PictaStudio\Venditio\Models\Currency;

use function PictaStudio\Venditio\Helpers\Functions\query;

class CurrencyController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return GenericModelResource::collection(
            $this->applyBaseFilters(query('currency'), request()->all(), 'currency')
        );
    }

    public function store(StoreCurrencyRequest $request): JsonResource
    {
        $payload = $request->validated();
        $countryIds = Arr::pull($payload, 'country_ids');

        $currency = query('currency')->create($payload);

        if (is_array($countryIds)) {
            $currency->countries()->sync($countryIds);
        }

        return GenericModelResource::make($currency);
    }

    public function show(Currency $currency): JsonResource
    {
        return GenericModelResource::make($currency);
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResource
    {
        $payload = $request->validated();
        $countryIds = Arr::pull($payload, 'country_ids');

        $currency->fill($payload);
        $currency->save();

        if (is_array($countryIds)) {
            $currency->countries()->sync($countryIds);
        }

        return GenericModelResource::make($currency->refresh());
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return response()->noContent();
    }
}
