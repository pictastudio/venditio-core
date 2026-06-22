<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\Order\{StoreOrderRequest, UpdateOrderRequest};
use PictaStudio\Venditio\Http\Resources\V1\OrderResource;
use PictaStudio\Venditio\Models\Order;
use PictaStudio\Venditio\Pipelines\Order\OrderCreationPipeline;
use PictaStudio\Venditio\Support\AddressSnapshot;

use function PictaStudio\Venditio\Helpers\Functions\{query, resolve_dto};

class OrderController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', Order::class);

        $includes = $this->resolveOrderIncludes();
        $filters = request()->except('include');
        $orders = query('order')->with($this->orderIndexRelationsForIncludes($includes));

        return OrderResource::collection(
            $this->applyBaseFilters(
                $orders,
                $filters,
                'order'
            )
        );
    }

    public function store(StoreOrderRequest $request, OrderCreationPipeline $pipeline): JsonResource
    {
        $this->authorizeIfConfigured('create', Order::class);

        $includes = $this->resolveOrderIncludes();
        $order = $pipeline->run(
            resolve_dto('order')::fromCart(
                query('cart')
                    ->where('status', config('venditio.cart.status_enum')::getActiveStatus())
                    ->findOrFail($request->validated('cart_id'))
            )
        );

        return OrderResource::make($order->load($this->orderRelationsForIncludes($includes)));
    }

    public function show(Order $order): JsonResource
    {
        $this->authorizeIfConfigured('view', $order);

        $includes = $this->resolveOrderIncludes();

        return OrderResource::make($order->load($this->orderRelationsForIncludes($includes)));
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResource
    {
        $this->authorizeIfConfigured('update', $order);

        $payload = $request->validated();

        if (array_key_exists('addresses', $payload)) {
            $payload['addresses'] = AddressSnapshot::collection($payload['addresses']) ?? [];
        }

        $order->fill($payload);
        $order->save();

        $includes = $this->resolveOrderIncludes();

        return OrderResource::make($order->refresh()->load($this->orderRelationsForIncludes($includes)));
    }

    public function destroy(Order $order)
    {
        $this->authorizeIfConfigured('delete', $order);

        $order->delete();

        return response()->noContent();
    }

    protected function resolveOrderIncludes(): array
    {
        return $this->resolveIncludes($this->allowedIncludesWithDiscounts([
            'lines',
            'payment_method',
            'shipping_method',
            'shipping_status',
            'shipping_zone',
            'user',
        ]));
    }

    protected function orderIndexRelationsForIncludes(array $includes): array
    {
        return $this->includedOrderRelations($includes);
    }

    protected function orderRelationsForIncludes(array $includes): array
    {
        return collect([
            'lines',
            'paymentMethod',
            'shippingMethod',
            'shippingZone',
        ])
            ->merge($this->includedOrderRelations($includes))
            ->unique()
            ->values()
            ->all();
    }

    protected function includedOrderRelations(array $includes): array
    {
        return collect([
            'lines' => 'lines',
            'payment_method' => 'paymentMethod',
            'shipping_method' => 'shippingMethod',
            'shipping_status' => 'shippingStatus',
            'shipping_zone' => 'shippingZone',
            'user' => 'user',
        ])
            ->filter(fn (string $relation, string $include): bool => in_array($include, $includes, true))
            ->values()
            ->merge($this->discountRelationsForIncludes($includes))
            ->unique()
            ->values()
            ->all();
    }
}
