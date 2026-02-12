<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\PriceListPrice\{StorePriceListPriceRequest, UpdatePriceListPriceRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\PriceListPriceResource;
use PictaStudio\VenditioCore\Models\PriceListPrice;

use function PictaStudio\VenditioCore\Helpers\Functions\{query, resolve_model};

class PriceListPriceController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('viewAny', resolve_model('price_list_price'));

        $filters = request()->all();

        $this->validateData($filters, [
            'product_id' => ['sometimes', 'integer', Rule::exists((new (resolve_model('product')))->getTable(), 'id')],
            'price_list_id' => ['sometimes', 'integer', Rule::exists((new (resolve_model('price_list')))->getTable(), 'id')],
        ]);

        return PriceListPriceResource::collection(
            $this->applyBaseFilters(
                query('price_list_price')
                    ->when(
                        isset($filters['product_id']),
                        fn ($builder) => $builder->where('product_id', (int) $filters['product_id'])
                    )
                    ->when(
                        isset($filters['price_list_id']),
                        fn ($builder) => $builder->where('price_list_id', (int) $filters['price_list_id'])
                    )
                    ->with('priceList'),
                $filters,
                'price_list_price'
            )
        );
    }

    public function store(StorePriceListPriceRequest $request): JsonResource
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('create', resolve_model('price_list_price'));

        $priceListPrice = query('price_list_price')->create($request->validated());
        $this->normalizeDefaultForProduct($priceListPrice);

        return PriceListPriceResource::make($priceListPrice->refresh()->load('priceList'));
    }

    public function show(PriceListPrice $priceListPrice): JsonResource
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('view', $priceListPrice);

        return PriceListPriceResource::make($priceListPrice->load('priceList'));
    }

    public function update(UpdatePriceListPriceRequest $request, PriceListPrice $priceListPrice): JsonResource
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('update', $priceListPrice);

        $priceListPrice->fill($request->validated());
        $priceListPrice->save();

        $this->normalizeDefaultForProduct($priceListPrice);

        return PriceListPriceResource::make($priceListPrice->refresh()->load('priceList'));
    }

    public function destroy(PriceListPrice $priceListPrice)
    {
        $this->ensureFeatureIsEnabled();
        $this->authorizeIfConfigured('delete', $priceListPrice);

        $priceListPrice->delete();

        return response()->noContent();
    }

    private function ensureFeatureIsEnabled(): void
    {
        abort_unless(config('venditio-core.price_lists.enabled', false), 404);
    }

    private function normalizeDefaultForProduct(PriceListPrice $priceListPrice): void
    {
        if (!$priceListPrice->is_default) {
            return;
        }

        query('price_list_price')
            ->where('product_id', $priceListPrice->product_id)
            ->whereKeyNot($priceListPrice->getKey())
            ->update(['is_default' => false]);
    }
}
