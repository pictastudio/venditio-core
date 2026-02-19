<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\DiscountApplication\{StoreDiscountApplicationRequest, UpdateDiscountApplicationRequest};
use PictaStudio\Venditio\Http\Resources\V1\GenericModelResource;
use PictaStudio\Venditio\Models\DiscountApplication;

use function PictaStudio\Venditio\Helpers\Functions\query;

class DiscountApplicationController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', DiscountApplication::class);

        return GenericModelResource::collection(
            $this->applyBaseFilters(query('discount_application'), request()->all(), 'discount_application')
        );
    }

    public function store(StoreDiscountApplicationRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', DiscountApplication::class);

        $discountApplication = query('discount_application')->create($request->validated());

        return GenericModelResource::make($discountApplication);
    }

    public function show(DiscountApplication $discountApplication): JsonResource
    {
        $this->authorizeIfConfigured('view', $discountApplication);

        return GenericModelResource::make($discountApplication);
    }

    public function update(UpdateDiscountApplicationRequest $request, DiscountApplication $discountApplication): JsonResource
    {
        $this->authorizeIfConfigured('update', $discountApplication);

        $discountApplication->fill($request->validated());
        $discountApplication->save();

        return GenericModelResource::make($discountApplication->refresh());
    }

    public function destroy(DiscountApplication $discountApplication)
    {
        $this->authorizeIfConfigured('delete', $discountApplication);

        $discountApplication->delete();

        return response()->noContent();
    }
}
