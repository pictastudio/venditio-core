<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Address\StoreAddressRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\BrandResource;
use PictaStudio\VenditioCore\Models\Brand;
use PictaStudio\VenditioCore\Models\Contracts\Brand as BrandContract;

class BrandController extends Controller
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

        $brands = app(BrandContract::class)::query()
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

        return BrandResource::collection($brands);
    }

    // public function store(StoreAddressRequest $request): JsonResource
    // {
    //     return BrandResource::make();
    // }

    public function show(Brand $brand): JsonResource
    {
        return BrandResource::make($brand);
    }

    // public function update(UpdateBrandRequest $request, Brand $brand): JsonResource
    // {
    //     return BrandResource::make();
    // }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return response()->noContent();
    }
}
