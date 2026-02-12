<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\CartLine\{StoreCartLineRequest, UpdateCartLineRequest};
use PictaStudio\Venditio\Http\Resources\V1\CartLineResource;
use PictaStudio\Venditio\Models\CartLine;

use function PictaStudio\Venditio\Helpers\Functions\query;

class CartLineController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return CartLineResource::collection(
            $this->applyBaseFilters(query('cart_line'), request()->all(), 'cart_line')
        );
    }

    public function store(StoreCartLineRequest $request): JsonResource
    {
        $cartLine = query('cart_line')->create($request->validated());

        return CartLineResource::make($cartLine);
    }

    public function show(CartLine $cartLine): JsonResource
    {
        return CartLineResource::make($cartLine);
    }

    public function update(UpdateCartLineRequest $request, CartLine $cartLine): JsonResource
    {
        $cartLine->fill($request->validated());
        $cartLine->save();

        return CartLineResource::make($cartLine->refresh());
    }

    public function destroy(CartLine $cartLine)
    {
        $cartLine->delete();

        return response()->noContent();
    }
}
