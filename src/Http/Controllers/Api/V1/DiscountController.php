<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Discount\{StoreDiscountRequest, UpdateDiscountRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\GenericModelResource;
use PictaStudio\VenditioCore\Models\Discount;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class DiscountController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return GenericModelResource::collection(
            $this->applyBaseFilters(query('discount'), request()->all(), 'discount')
        );
    }

    public function store(StoreDiscountRequest $request): JsonResource
    {
        $discount = query('discount')->create($request->validated());

        return GenericModelResource::make($discount);
    }

    public function show(Discount $discount): JsonResource
    {
        return GenericModelResource::make($discount);
    }

    public function update(UpdateDiscountRequest $request, Discount $discount): JsonResource
    {
        $discount->fill($request->validated());
        $discount->save();

        return GenericModelResource::make($discount->refresh());
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return response()->noContent();
    }
}
