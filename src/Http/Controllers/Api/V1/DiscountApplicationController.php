<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\DiscountApplication\{StoreDiscountApplicationRequest, UpdateDiscountApplicationRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\GenericModelResource;
use PictaStudio\VenditioCore\Models\DiscountApplication;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class DiscountApplicationController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        return GenericModelResource::collection(
            $this->applyBaseFilters(query('discount_application'), request()->all(), 'discount_application')
        );
    }

    public function store(StoreDiscountApplicationRequest $request): JsonResource
    {
        $discountApplication = query('discount_application')->create($request->validated());

        return GenericModelResource::make($discountApplication);
    }

    public function show(DiscountApplication $discountApplication): JsonResource
    {
        return GenericModelResource::make($discountApplication);
    }

    public function update(UpdateDiscountApplicationRequest $request, DiscountApplication $discountApplication): JsonResource
    {
        $discountApplication->fill($request->validated());
        $discountApplication->save();

        return GenericModelResource::make($discountApplication->refresh());
    }

    public function destroy(DiscountApplication $discountApplication)
    {
        $discountApplication->delete();

        return response()->noContent();
    }
}
