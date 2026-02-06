<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Product\StoreProductRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\Product\UpdateProductRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductResource;
use PictaStudio\VenditioCore\Actions\Products\CreateProduct;
use PictaStudio\VenditioCore\Actions\Products\UpdateProduct;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', Product::class);

        $filters = request()->all();

        return ProductResource::collection(
            $this->applyBaseFilters(query('product'), $filters, 'product')
        );
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorizeIfConfigured('create', Product::class);

        $product = app(CreateProduct::class)
            ->handle($request->validated());

        return ProductResource::make($product);
    }

    public function show(Product $product): JsonResource
    {
        $this->authorizeIfConfigured('view', $product);

        return ProductResource::make($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorizeIfConfigured('update', $product);

        $product = app(UpdateProduct::class)
            ->handle($product, $request->validated());

        return ProductResource::make($product);
    }

    public function destroy(Product $product)
    {
        $this->authorizeIfConfigured('delete', $product);

        $product->delete();

        return response()->noContent();
    }
}
