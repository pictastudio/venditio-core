<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
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

        $includes = $this->resolveProductIncludes();
        $filters = request()->all();

        return ProductResource::collection(
            $this->applyBaseFilters(
                query('product')->with($this->productRelationsForIncludes($includes)),
                $filters,
                'product'
            )
        );
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorizeIfConfigured('create', Product::class);

        $includes = $this->resolveProductIncludes();

        $product = app(CreateProduct::class)
            ->handle($request->validated());

        return ProductResource::make($product->load($this->productRelationsForIncludes($includes)));
    }

    public function show(Product $product): JsonResource
    {
        $this->authorizeIfConfigured('view', $product);

        $includes = $this->resolveProductIncludes();

        return ProductResource::make($product->load($this->productRelationsForIncludes($includes)));
    }

    public function variants(Product $product): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('view', $product);

        $includes = $this->resolveProductIncludes();
        $filters = request()->all();

        $variants = $this->applyBaseFilters(
            query('product')
                ->where('parent_id', $product->getKey())
                ->with($this->productRelationsForIncludes($includes)),
            $filters,
            'product'
        );

        return ProductResource::collection($variants);
    }

    public function createVariants(GenerateProductVariantsRequest $request, Product $product, CreateProductVariants $action): JsonResponse
    {
        $this->authorizeIfConfigured('update', $product);

        $includes = $this->resolveProductIncludes();
        $result = $action->execute($product, $request->validated('variants'));
        $created = $result['created']->load($this->productRelationsForIncludes($includes));

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

        $includes = $this->resolveProductIncludes();

        $product = app(UpdateProduct::class)
            ->handle($product, $request->validated());

        return ProductResource::make($product->load($this->productRelationsForIncludes($includes)));
    }

    public function destroy(Product $product)
    {
        $this->authorizeIfConfigured('delete', $product);

        $product->delete();

        return response()->noContent();
    }

    protected function resolveProductIncludes(): array
    {
        $rawIncludes = request()->query('include', []);

        $includes = collect(is_array($rawIncludes) ? $rawIncludes : [$rawIncludes])
            ->flatMap(
                fn (mixed $include) => is_string($include) ? explode(',', $include) : []
            )
            ->map(fn (string $include) => trim($include))
            ->filter(fn (string $include) => filled($include))
            ->unique()
            ->values()
            ->all();

        $this->validateData([
            'include' => $includes,
        ], [
            'include' => ['array'],
            'include.*' => [
                'string',
                Rule::in(['variants', 'variants_options_table']),
            ],
        ]);

        return $includes;
    }

    protected function productRelationsForIncludes(array $includes): array
    {
        $relations = ['variantOptions.productVariant', 'inventory'];

        if (in_array('variants', $includes, true) || in_array('variants_options_table', $includes, true)) {
            $relations[] = 'variants.variantOptions.productVariant';
            $relations[] = 'variants.inventory';
        }

        return $relations;
    }
}
