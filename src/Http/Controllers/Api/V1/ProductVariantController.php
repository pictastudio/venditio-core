<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\Venditio\Actions\ProductVariants\{CreateProductVariant, UpdateProductVariant};
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\ProductVariant\{StoreProductVariantRequest, UpdateProductVariantRequest};
use PictaStudio\Venditio\Http\Resources\V1\ProductVariantResource;
use PictaStudio\Venditio\Models\ProductVariant;

use function PictaStudio\Venditio\Helpers\Functions\{query, resolve_model};

class ProductVariantController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', ProductVariant::class);

        $filters = request()->all();

        $this->validateData($filters, [
            'product_type_id' => [
                'sometimes',
                'integer',
                Rule::exists((new (resolve_model('product_type')))->getTable(), 'id'),
            ],
        ]);

        $query = query('product_variant')
            ->when(
                isset($filters['product_type_id']),
                fn ($query) => $query->where('product_type_id', $filters['product_type_id'])
            );

        return ProductVariantResource::collection(
            $this->applyBaseFilters($query, $filters, 'product_variant')
        );
    }

    public function store(StoreProductVariantRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', ProductVariant::class);

        $variant = app(CreateProductVariant::class)
            ->handle($request->validated());

        return ProductVariantResource::make($variant);
    }

    public function show(ProductVariant $productVariant): JsonResource
    {
        $this->authorizeIfConfigured('view', $productVariant);

        return ProductVariantResource::make($productVariant);
    }

    public function update(UpdateProductVariantRequest $request, ProductVariant $productVariant): JsonResource
    {
        $this->authorizeIfConfigured('update', $productVariant);

        $productVariant = app(UpdateProductVariant::class)
            ->handle($productVariant, $request->validated());

        return ProductVariantResource::make($productVariant);
    }

    public function destroy(ProductVariant $productVariant)
    {
        $this->authorizeIfConfigured('delete', $productVariant);

        $productVariant->delete();

        return response()->noContent();
    }
}
