<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Dto\CartDto;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Cart\StoreCartRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\Cart\UpdateCartRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\CartResource;
use PictaStudio\VenditioCore\Models\Cart;
use PictaStudio\VenditioCore\Pipelines\Cart\CartCreationPipeline;
use PictaStudio\VenditioCore\Pipelines\Cart\CartUpdatePipeline;

class CartController extends Controller
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

        $carts = Cart::query()
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

        return CartResource::collection($carts);
    }

    public function store(StoreCartRequest $request, CartCreationPipeline $pipeline): JsonResource
    {
        $cart = $pipeline->run(
            CartDto::fromArray(array_merge(
                $request->validated(),
                ['cart' => (new Cart)->updateTimestamps()]
            ))
        );

        return CartResource::make($cart);
    }

    public function show(Cart $cart): JsonResource
    {
        return CartResource::make($cart);
    }

    public function update(UpdateCartRequest $request, Cart $cart, CartUpdatePipeline $pipeline): JsonResource
    {
        $cart = $pipeline->run(
            CartDto::fromArray(array_merge(
                $request->validated(),
                ['cart' => $cart]
            ))
        );

        return CartResource::make($cart);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->noContent();
    }
}
