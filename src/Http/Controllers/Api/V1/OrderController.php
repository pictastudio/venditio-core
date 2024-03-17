<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Order\StoreOrderRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\Order\UpdateOrderRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\OrderResource;
use PictaStudio\VenditioCore\Models\Contracts\Cart as CartContract;
use PictaStudio\VenditioCore\Models\Contracts\Order as OrderContract;
use PictaStudio\VenditioCore\Models\Order;
use PictaStudio\VenditioCore\Pipelines\Order\OrderCreationPipeline;

class OrderController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $filters = request()->all();
        $hasFilters = count($filters) > 0;

        if ($hasFilters) {
            $validationResponse = $this->validateData($filters, [
                'all' => [
                    'boolean',
                ],
                'ids' => [
                    'array',
                ],
                'ids.*' => [
                    Rule::exists('product_items', 'id'),
                ],
            ]);

            if ($validationResponse instanceof JsonResponse) {
                return $validationResponse;
            }

            $filters = $validationResponse;
        }

        $orders = app(OrderContract::class)::query()
            ->when(
                $hasFilters && isset($filters['ids']),
                fn (Builder $query) => $query->whereIn('id', $filters['ids'])
            )
            ->when(
                $hasFilters && isset($filters['all']),
                fn (Builder $query) => $query->get(),
                fn (Builder $query) => $query->paginate(
                    request('per_page', config('venditio-core.routes.api.v1.pagination.per_page'))
                ),
            );

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request, OrderCreationPipeline $pipeline): JsonResource
    {
        $order = $pipeline->run(
            app(OrderDtoContract::class)::fromCart(
                app(CartContract::class)::findOrFail($request->input('cart_id'))
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
        return OrderResource::make($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
