<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Actions\ProductTypes\{CreateProductType, UpdateProductType};
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\ProductType\{StoreProductTypeRequest, UpdateProductTypeRequest};
use PictaStudio\Venditio\Http\Resources\V1\ProductTypeResource;
use PictaStudio\Venditio\Models\ProductType;

use function PictaStudio\Venditio\Helpers\Functions\query;

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
