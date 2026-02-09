<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Actions\ProductTypes\CreateProductType;
use PictaStudio\VenditioCore\Actions\ProductTypes\UpdateProductType;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductType\StoreProductTypeRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductType\UpdateProductTypeRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductTypeResource;
use PictaStudio\VenditioCore\Models\ProductType;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductTypeController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', ProductType::class);

        $filters = request()->all();

        return ProductTypeResource::collection(
            $this->applyBaseFilters(query('product_type'), $filters, 'product_type')
        );
    }

    public function store(StoreProductTypeRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', ProductType::class);

        $productType = app(CreateProductType::class)
            ->handle($request->validated());

        return ProductTypeResource::make($productType);
    }

    public function show(ProductType $productType): JsonResource
    {
        $this->authorizeIfConfigured('view', $productType);

        return ProductTypeResource::make($productType);
    }

    public function update(UpdateProductTypeRequest $request, ProductType $productType): JsonResource
    {
        $this->authorizeIfConfigured('update', $productType);

        $productType = app(UpdateProductType::class)
            ->handle($productType, $request->validated());

        return ProductTypeResource::make($productType);
    }

    public function destroy(ProductType $productType)
    {
        $this->authorizeIfConfigured('delete', $productType);

        $productType->delete();

        return response()->noContent();
    }
}
