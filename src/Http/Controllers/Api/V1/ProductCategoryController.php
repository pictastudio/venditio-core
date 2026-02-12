<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Actions\ProductCategories\{CreateProductCategory, UpdateProductCategory};
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\ProductCategory\{StoreProductCategoryRequest, UpdateProductCategoryRequest};
use PictaStudio\Venditio\Http\Resources\V1\ProductCategoryResource;
use PictaStudio\Venditio\Models\ProductCategory;

use function PictaStudio\Venditio\Helpers\Functions\query;

class ProductCategoryController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', ProductCategory::class);

        $filters = request()->all();
        $this->validateData($filters, [
            'as_tree' => [
                'boolean',
            ],
        ]);

        $asTree = request()->boolean('as_tree');
        unset($filters['as_tree']);

        if ($asTree) {
            return ProductCategoryResource::collection(
                $this->applyBaseFilters(
                    query('product_category'),
                    [
                        ...$filters,
                        'all' => true,
                    ],
                    'product_category'
                )->tree()
            );
        }

        return ProductCategoryResource::collection(
            $this->applyBaseFilters(query('product_category'), $filters, 'product_category')
        );
    }

    public function store(StoreProductCategoryRequest $request)
    {
        $this->authorizeIfConfigured('create', ProductCategory::class);

        $category = app(CreateProductCategory::class)
            ->handle($request->validated());

        return ProductCategoryResource::make($category);
    }

    public function show(ProductCategory $productCategory): JsonResource
    {
        $this->authorizeIfConfigured('view', $productCategory);

        return ProductCategoryResource::make($productCategory);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $this->authorizeIfConfigured('update', $productCategory);

        $category = app(UpdateProductCategory::class)
            ->handle($productCategory, $request->validated());

        return ProductCategoryResource::make($category);
    }

    public function destroy(ProductCategory $productCategory)
    {
        $this->authorizeIfConfigured('delete', $productCategory);

        $productCategory->delete();

        return response()->noContent();
    }
}
