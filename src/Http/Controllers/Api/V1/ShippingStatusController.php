<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\ShippingStatus\{StoreShippingStatusRequest, UpdateShippingStatusRequest};
use PictaStudio\Venditio\Http\Resources\V1\GenericModelResource;
use PictaStudio\Venditio\Models\ShippingStatus;

use function PictaStudio\Venditio\Helpers\Functions\query;

class ShippingStatusController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return GenericModelResource::collection(
            $this->applyBaseFilters(query('shipping_status'), request()->all(), 'shipping_status')
        );
    }

    public function store(StoreShippingStatusRequest $request): JsonResource
    {
        $shippingStatus = query('shipping_status')->create($request->validated());

        return GenericModelResource::make($shippingStatus);
    }

    public function show(ShippingStatus $shippingStatus): JsonResource
    {
        return GenericModelResource::make($shippingStatus);
    }

    public function update(UpdateShippingStatusRequest $request, ShippingStatus $shippingStatus): JsonResource
    {
        $shippingStatus->fill($request->validated());
        $shippingStatus->save();

        return GenericModelResource::make($shippingStatus->refresh());
    }

    public function destroy(ShippingStatus $shippingStatus)
    {
        $shippingStatus->delete();

        return response()->noContent();
    }
}
