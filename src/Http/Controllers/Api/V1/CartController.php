<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Dto\CartDto;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Cart\StoreCartRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\Cart\UpdateCartRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\CartResource;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;
use PictaStudio\VenditioCore\Pipelines\Cart\CartCreationPipeline;
use PictaStudio\VenditioCore\Pipelines\Cart\CartUpdatePipeline;
use PictaStudio\VenditioCore\Validations\Contracts\CartLineValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\query;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_dto;

class CartController extends Controller
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
                Rule::exists('carts', 'id'),
            ],
        ]);

        return CartResource::collection(
            query('cart')
                ->when(
                    isset($filters['id']),
                    fn (Builder $query) => $query->whereIn('id', $filters['id']),
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

    public function store(StoreCartRequest $request, CartCreationPipeline $pipeline): JsonResource
    {
        return CartResource::make(
            $pipeline->run(
                resolve_dto('cart')::fromArray($request->validated())
            )
        );
    }

    public function show(Cart $cart): JsonResource
    {
        return CartResource::make($cart->load('lines'));
    }

    public function update(UpdateCartRequest $request, Cart $cart, CartUpdatePipeline $pipeline): JsonResource
    {
        return CartResource::make(
            $pipeline->run(
                resolve_dto('cart')::fromArray(
                    array_merge(
                        $request->validated(),
                        ['cart' => $cart]
                    )
                )
            )
        );
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json([
            'message' => 'Cart deleted successfully',
        ]);
    }

    public function addLines(Cart $cart, CartLineValidationRules $cartLineValidationRules)
    {
        $validationResponse = $this->validateData(request()->all(), $cartLineValidationRules->getStoreValidationRules());

        if ($validationResponse instanceof JsonResponse) {
            return $validationResponse;
        }

        $cart->lines()->createMany($validationResponse['lines']);

        return response()->json([
            'message' => 'Lines added successfully',
        ]);
    }

    public function updateLines(Cart $cart, CartLineValidationRules $cartLineValidationRules)
    {
        $validationResponse = $this->validateData(request()->all(), $cartLineValidationRules->getUpdateValidationRules());

        if ($validationResponse instanceof JsonResponse) {
            return $validationResponse;
        }

        // pipeline per update cart lines

        foreach ($validationResponse['lines'] as $line) {
            $cart->lines()->find($line['id'])->update([
                'qty' => $line['qty'],
            ]);
        }

        return response()->json([
            'message' => 'Lines updated successfully',
        ]);
    }
}
