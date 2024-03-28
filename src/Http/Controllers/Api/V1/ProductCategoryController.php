<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductCategory\StoreProductCategoryRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductCategory\UpdateProductCategoryRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductCategoryResource;
use PictaStudio\VenditioCore\Models\Contracts\ProductCategory as ProductCategoryContract;
use PictaStudio\VenditioCore\Models\ProductCategory;

class ProductCategoryController extends Controller
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

        $productCategories = app(ProductCategoryContract::class)::query()
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

        return ProductCategoryResource::collection($productCategories);
    }

    public function store(StoreProductCategoryRequest $request)
    {

    }

    public function show(ProductCategory $productCategory): JsonResource
    {
        return ProductCategoryResource::make($productCategory);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {

    }

    public function destroy(ProductCategory $productCategory)
    {

    }
}
