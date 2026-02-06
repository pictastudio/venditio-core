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
use PictaStudio\VenditioCore\Actions\ProductCategories\CreateProductCategory;
use PictaStudio\VenditioCore\Actions\ProductCategories\UpdateProductCategory;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductCategoryController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', ProductCategory::class);

        $filters = request()->all();

        // $this->validateData($filters, [
        //     'all' => [
        //         'boolean',
        //     ],
        //     'id' => [
        //         'array',
        //     ],
        //     'id.*' => [
        //         Rule::exists('product_categories', 'id'),
        //     ],
        // ]);

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
