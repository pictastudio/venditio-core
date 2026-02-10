<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Actions\Products\{CreateProduct, CreateProductVariants, UpdateProduct};
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Product\{GenerateProductVariantsRequest, StoreProductRequest, UpdateProductRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\ProductResource;
use PictaStudio\VenditioCore\Models\Product;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', Product::class);

        $filters = request()->all();

        return ProductResource::collection(
            $this->applyBaseFilters(
                query('product')->with(['variantOptions', 'inventory']),
                $filters,
                'product'
            )
        );
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorizeIfConfigured('create', Product::class);

        $product = app(CreateProduct::class)
            ->handle($request->validated());

        return ProductResource::make($product->load(['variantOptions', 'inventory']));
    }

    public function show(Product $product): JsonResource
    {
        $this->authorizeIfConfigured('view', $product);

        return ProductResource::make($product->load(['variantOptions', 'inventory']));
    }

    public function variants(Product $product): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('view', $product);

        $filters = request()->all();

        $variants = $this->applyBaseFilters(
            query('product')
                ->where('parent_id', $product->getKey())
                ->with(['variantOptions', 'inventory']),
            $filters,
            'product'
        );

        return ProductResource::collection($variants);
    }

    public function createVariants(GenerateProductVariantsRequest $request, Product $product, CreateProductVariants $action): JsonResponse
    {
        $this->authorizeIfConfigured('update', $product);

        $result = $action->execute($product, $request->validated('variants'));
        $created = $result['created']->load(['variantOptions', 'inventory']);

        return response()->json([
            'data' => ProductResource::collection($created),
            'meta' => [
                'created' => $created->count(),
                'skipped' => count($result['skipped']),
                'total' => $result['total'],
            ],
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorizeIfConfigured('update', $product);

        $product = app(UpdateProduct::class)
            ->handle($product, $request->validated());

        return ProductResource::make($product->load(['variantOptions', 'inventory']));
    }

    public function destroy(Product $product)
    {
        $this->authorizeIfConfigured('delete', $product);

        $product->delete();

        return response()->noContent();
    }
}
