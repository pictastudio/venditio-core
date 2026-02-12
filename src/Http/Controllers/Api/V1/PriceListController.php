<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\PriceList\{StorePriceListRequest, UpdatePriceListRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\PriceListResource;
use PictaStudio\VenditioCore\Models\PriceList;

use function PictaStudio\VenditioCore\Helpers\Functions\{query, resolve_model};

class PriceListController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('viewAny', resolve_model('price_list'));

        return PriceListResource::collection(
            $this->applyBaseFilters(
                query('price_list'),
                request()->all(),
                'price_list'
            )
        );
    }

    public function store(StorePriceListRequest $request): JsonResource
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('create', resolve_model('price_list'));

        $priceList = query('price_list')->create($request->validated());

        return PriceListResource::make($priceList->refresh());
    }

    public function show(PriceList $priceList): JsonResource
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('view', $priceList);

        return PriceListResource::make($priceList);
    }

    public function update(UpdatePriceListRequest $request, PriceList $priceList): JsonResource
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('update', $priceList);

        $priceList->fill($request->validated());
        $priceList->save();

        return PriceListResource::make($priceList->refresh());
    }

    public function destroy(PriceList $priceList)
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('delete', $priceList);

        $priceList->delete();

        return response()->noContent();
    }

    private function ensureFeatureIsEnabled(): void
    {
        abort_unless(config('venditio-core.price_lists.enabled', false), 404);
    }
}
