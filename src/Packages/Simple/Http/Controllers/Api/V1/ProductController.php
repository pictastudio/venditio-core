<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\{StoreProductItemRequest, UpdateProductItemRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\ProductResource;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductItem;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductController extends Controller
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
        //         Rule::exists('products', 'id'),
        //     ],
        // ]);

        return ProductResource::collection(
            $this->applyBaseFilters(query('product'), $filters, 'product')
        );
    }

    public function store(StoreProductItemRequest $request) {}

    public function show(ProductItem $productItem): JsonResource
    {
        return ProductResource::make($productItem);
    }

    public function update(UpdateProductItemRequest $request, ProductItem $productItem) {}

    public function destroy(ProductItem $productItem) {}
}
