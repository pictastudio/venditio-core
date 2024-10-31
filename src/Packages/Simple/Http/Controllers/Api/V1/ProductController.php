<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\StoreProductItemRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\UpdateProductItemRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductResource;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductItem;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $filters = request()->all();

        $this->validateData($filters, [
            'all' => [
                'boolean',
            ],
            'id' => [
                'array',
            ],
            'id.*' => [
                Rule::exists('products', 'id'),
            ],
        ]);

        return ProductResource::collection(
            query('product')
                ->when(
                    isset($filters['id']),
                    fn (Builder $query) => $query->whereIn('id', $filters['id'])
                )
                ->when(
                    isset($filters['all']),
                    fn (Builder $query) => $query->get(),
                    fn (Builder $query) => $query->paginate(
                        request('per_page', config('venditio-core.routes.api.v1.pagination.per_page'))
                    ),
                )
        );
    }

    public function store(StoreProductItemRequest $request)
    {

    }

    public function show(ProductItem $productItem): JsonResource
    {
        return ProductResource::make($productItem);
    }

    public function update(UpdateProductItemRequest $request, ProductItem $productItem)
    {

    }

    public function destroy(ProductItem $productItem)
    {

    }
}
