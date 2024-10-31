<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Facades\VenditioCore;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\StoreProductItemRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\ProductItem\UpdateProductItemRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\ProductResource;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductItem;
use PictaStudio\VenditioCore\Packages\Simple\Http\Controllers\Api\V1\ProductController as SimpleProductController;
use PictaStudio\VenditioCore\Packages\Advanced\Http\Controllers\Api\V1\ProductController as AdvancedProductController;
use PictaStudio\VenditioCore\Packages\Tools\PackageType;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ProductController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        if (VenditioCore::isSimple()) {
            return app(SimpleProductController::class)->index();
        }

        return app(AdvancedProductController::class)->index();
    }

    public function store(StoreProductItemRequest $request)
    {
        // $this->authorize('create', ProductItem::class);

        // $productItem = ProductItem::create($request->all());

        // return ProductResource::make($productItem);
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
