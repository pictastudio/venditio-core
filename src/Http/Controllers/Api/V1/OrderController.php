<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Order\{StoreOrderRequest, UpdateOrderRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\OrderResource;
use PictaStudio\VenditioCore\Models\Order;
use PictaStudio\VenditioCore\Pipelines\Order\OrderCreationPipeline;

use function PictaStudio\VenditioCore\Helpers\Functions\{query, resolve_dto};

class OrderController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $filters = request()->all();

        // $this->validateData($filters, [
        //     'all' => [
        //         'boolean',
        //     ],
        //     'id' => [
        //         'array',
        //     ],
        //     'id.*' => [
        //         Rule::exists('orders', 'id'),
        //     ],
        // ]);

        return OrderResource::collection(
            $this->applyBaseFilters(query('order'), $filters, 'order')
        );
    }

    public function store(StoreOrderRequest $request, OrderCreationPipeline $pipeline): JsonResource
    {
        $order = $pipeline->run(
            resolve_dto('order')::fromCart(
                query('cart')
                    ->where('status', config('venditio-core.cart.status_enum')::getActiveStatus())
                    ->findOrFail($request->validated('cart_id'))
            )
        );

        return OrderResource::make($order);
    }

    public function show(Order $order): JsonResource
    {
        return OrderResource::make($order->load('lines'));
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResource
    {
        $order->fill($request->validated());
        $order->save();

        return OrderResource::make($order->refresh()->load('lines'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
