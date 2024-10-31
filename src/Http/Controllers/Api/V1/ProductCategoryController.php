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
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductCategoryController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $filters = request()->all();

        $this->validateData($filters, [
            'all' => [
                'boolean',
            ],
            'id' => [
                'array',
            ],
            'id.*' => [
                Rule::exists('product_categories', 'id'),
            ],
        ]);

        return ProductCategoryResource::collection(
            query('product_category')
                ->when(
                    isset($filters['id']),
                    fn (Builder $query) => $query->whereIn('id', $filters['id'])
                )
                ->when(
                    isset($filters['all']),
                    fn (Builder $query) => $query->get(),
                    fn (Builder $query) => $query->paginate(
                        request('per_page', config('venditio-core.routes.api.v1.pagination.per_page'))
                    ),
                )
        );
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
