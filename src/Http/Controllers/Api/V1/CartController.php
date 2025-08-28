<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Cart\{StoreCartRequest, UpdateCartRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\CartResource;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;
use PictaStudio\VenditioCore\Pipelines\Cart\{CartCreationPipeline, CartUpdatePipeline};
use PictaStudio\VenditioCore\Validations\Contracts\CartLineValidationRules;

use function PictaStudio\VenditioCore\Helpers\Functions\{query, resolve_dto};

class CartController extends Controller
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
        //         Rule::exists('carts', 'id'),
        //     ],
        // ]);

        return CartResource::collection(
            $this->applyBaseFilters(query('cart'), $filters, 'cart')
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

    public function destroy(Cart $cart): JsonResponse
    {
        $cart->purge();

        return $this->successJsonResponse(
            message: 'Cart deleted successfully',
        );
    }

    public function addLines(Cart $cart, CartLineValidationRules $cartLineValidationRules): JsonResponse
    {
        $validationResponse = $this->validateData(request()->all(), $cartLineValidationRules->getStoreValidationRules());

        $cart->lines()->createMany($validationResponse['lines']);

        return $this->successJsonResponse(
            message: 'Lines added successfully',
        );
    }

    public function updateLines(Cart $cart, CartLineValidationRules $cartLineValidationRules): JsonResponse
    {
        $validationResponse = $this->validateData(request()->all(), $cartLineValidationRules->getUpdateValidationRules());

        // pipeline per update cart lines

        foreach ($validationResponse['lines'] as $line) {
            $cart->lines()->find($line['id'])->update([
                'qty' => $line['qty'],
            ]);
        }

        return $this->successJsonResponse(
            message: 'Lines updated successfully',
        );
    }
}
