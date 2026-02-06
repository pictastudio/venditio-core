<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Actions\ProductVariantOptions\CreateProductVariantOption;
use PictaStudio\VenditioCore\Actions\ProductVariantOptions\UpdateProductVariantOption;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductVariantOption\StoreProductVariantOptionRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductVariantOption\UpdateProductVariantOptionRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductVariantOptionResource;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariantOption;

use function PictaStudio\VenditioCore\Helpers\Functions\query;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class ProductVariantOptionController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', ProductVariantOption::class);

        $filters = request()->all();

        $this->validateData($filters, [
            'product_variant_id' => [
                'sometimes',
                'integer',
                Rule::exists((new (resolve_model('product_variant')))->getTable(), 'id'),
            ],
        ]);

        $query = query('product_variant_option')
            ->when(
                isset($filters['product_variant_id']),
                fn ($query) => $query->where('product_variant_id', $filters['product_variant_id'])
            );

        return ProductVariantOptionResource::collection(
            $this->applyBaseFilters($query, $filters, 'product_variant_option')
        );
    }

    public function store(StoreProductVariantOptionRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', ProductVariantOption::class);

        $option = app(CreateProductVariantOption::class)
            ->handle($request->validated());

        return ProductVariantOptionResource::make($option);
    }

    public function show(ProductVariantOption $productVariantOption): JsonResource
    {
        $this->authorizeIfConfigured('view', $productVariantOption);

        return ProductVariantOptionResource::make($productVariantOption);
    }

    public function update(UpdateProductVariantOptionRequest $request, ProductVariantOption $productVariantOption): JsonResource
    {
        $this->authorizeIfConfigured('update', $productVariantOption);

        $productVariantOption = app(UpdateProductVariantOption::class)
            ->handle($productVariantOption, $request->validated());

        return ProductVariantOptionResource::make($productVariantOption);
    }

    public function destroy(ProductVariantOption $productVariantOption)
    {
        $this->authorizeIfConfigured('delete', $productVariantOption);

        $productVariantOption->delete();

        return response()->noContent();
    }
}
