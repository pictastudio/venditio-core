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
use PictaStudio\VenditioCore\Packages\Simple\Models\Order;
use PictaStudio\VenditioCore\Pipelines\Order\OrderCreationPipeline;

use function PictaStudio\VenditioCore\Helpers\Functions\query;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_dto;

class OrderController extends Controller
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
                Rule::exists('orders', 'id'),
            ],
        ]);

        return OrderResource::collection(
            query('order')
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

    public function store(StoreOrderRequest $request, OrderCreationPipeline $pipeline): JsonResource
    {
        $order = $pipeline->run(
            resolve_dto('order')::fromCart(
                query('cart')
                    ->where('status', config('venditio-core.carts.status_enum')::getActiveStatus())
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
        return OrderResource::make($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
