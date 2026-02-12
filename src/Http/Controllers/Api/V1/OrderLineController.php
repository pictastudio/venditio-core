<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\OrderLine\{StoreOrderLineRequest, UpdateOrderLineRequest};
use PictaStudio\Venditio\Http\Resources\V1\OrderLineResource;
use PictaStudio\Venditio\Models\OrderLine;

use function PictaStudio\Venditio\Helpers\Functions\query;

class OrderLineController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return OrderLineResource::collection(
            $this->applyBaseFilters(query('order_line'), request()->all(), 'order_line')
        );
    }

    public function store(StoreOrderLineRequest $request): JsonResource
    {
        $orderLine = query('order_line')->create($request->validated());

        return OrderLineResource::make($orderLine);
    }

    public function show(OrderLine $orderLine): JsonResource
    {
        return OrderLineResource::make($orderLine);
    }

    public function update(UpdateOrderLineRequest $request, OrderLine $orderLine): JsonResource
    {
        $orderLine->fill($request->validated());
        $orderLine->save();

        return OrderLineResource::make($orderLine->refresh());
    }

    public function destroy(OrderLine $orderLine)
    {
        $orderLine->delete();

        return response()->noContent();
    }
}
