<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\StoreProductItemRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\UpdateProductItemRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductItemResource;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductItem;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductItemController extends Controller
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
        //         Rule::exists('product_items', 'id'),
        //     ],
        // ]);

        return ProductItemResource::collection(
            $this->applyBaseFilters(query('product_item'), $filters, 'product_item')
        );
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
