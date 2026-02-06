<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Product\StoreProductRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\Product\UpdateProductRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductResource;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $filters = request()->all();

        return ProductResource::collection(
            $this->applyBaseFilters(query('product'), $filters, 'product')
        );
    }

    public function store(StoreProductRequest $request)
    {
        // $this->authorize('create', Product::class);

        // $product = Product::create($request->all());

        // return ProductResource::make($product);
    }

    public function show(Product $product): JsonResource
    {
        return ProductResource::make($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {

    }

    public function destroy(Product $product)
    {

    }
}
