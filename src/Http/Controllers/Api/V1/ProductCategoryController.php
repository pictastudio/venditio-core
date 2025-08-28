<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductCategory\{StoreProductCategoryRequest, UpdateProductCategoryRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\ProductCategoryResource;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductCategoryController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
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

    public function store(StoreProductCategoryRequest $request) {}

    public function show(ProductCategory $productCategory): JsonResource
    {
        return ProductCategoryResource::make($productCategory);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory) {}

    public function destroy(ProductCategory $productCategory) {}
}
