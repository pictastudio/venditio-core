<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\StoreProductItemRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\UpdateProductItemRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductItemResource;
use PictaStudio\VenditioCore\Models\Contracts\ProductItem as ProductItemContract;
use PictaStudio\VenditioCore\Models\ProductItem;

class ProductItemController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $filters = request()->all();
        $hasFilters = count($filters) > 0;

        if ($hasFilters) {
            $validationResponse = $this->validateData($filters, [
                'all' => [
                    'boolean',
                ],
                'ids' => [
                    'array',
                ],
                'ids.*' => [
                    Rule::exists('product_items', 'id'),
                ],
            ]);

            if ($validationResponse instanceof JsonResponse) {
                return $validationResponse;
            }

            $filters = $validationResponse;
        }

        $productItems = app(ProductItemContract::class)::query()
            ->when(
                $hasFilters && isset($filters['ids']),
                fn (Builder $query) => $query->whereIn('id', $filters['ids'])
            )
            ->when(
                $hasFilters && isset($filters['all']),
                fn (Builder $query) => $query->get(),
                fn (Builder $query) => $query->paginate(
                    request('per_page', config('venditio-core.routes.api.v1.pagination.per_page'))
                ),
            );

        return ProductItemResource::collection($productItems);
    }

    public function store(StoreProductItemRequest $request)
    {

    }

    public function show(ProductItem $productItem): JsonResource
    {
        return ProductItemResource::make($productItem);
    }

    public function update(UpdateProductItemRequest $request, ProductItem $productItem)
    {

    }

    public function destroy(ProductItem $productItem)
    {

    }
}
