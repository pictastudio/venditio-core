<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\ProductCustomField\{StoreProductCustomFieldRequest, UpdateProductCustomFieldRequest};
use PictaStudio\Venditio\Http\Resources\V1\GenericModelResource;
use PictaStudio\Venditio\Models\ProductCustomField;

use function PictaStudio\Venditio\Helpers\Functions\query;

class ProductCustomFieldController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return GenericModelResource::collection(
            $this->applyBaseFilters(query('product_custom_field'), request()->all(), 'product_custom_field')
        );
    }

    public function store(StoreProductCustomFieldRequest $request): JsonResource
    {
        $productCustomField = query('product_custom_field')->create($request->validated());

        return GenericModelResource::make($productCustomField);
    }

    public function show(ProductCustomField $productCustomField): JsonResource
    {
        return GenericModelResource::make($productCustomField);
    }

    public function update(UpdateProductCustomFieldRequest $request, ProductCustomField $productCustomField): JsonResource
    {
        $productCustomField->fill($request->validated());
        $productCustomField->save();

        return GenericModelResource::make($productCustomField->refresh());
    }

    public function destroy(ProductCustomField $productCustomField)
    {
        $productCustomField->delete();

        return response()->noContent();
    }
}
