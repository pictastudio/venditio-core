<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Brand\{StoreBrandRequest, UpdateBrandRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\BrandResource;
use PictaStudio\VenditioCore\Models\Brand;

use function PictaStudio\VenditioCore\Helpers\Functions\{query, resolve_model};

class BrandController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', resolve_model('brand'));

        $filters = request()->all();

        return BrandResource::collection(
            $this->applyBaseFilters(query('brand'), $filters, 'brand')
        );
    }

    public function store(StoreBrandRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', resolve_model('brand'));

        return BrandResource::make(
            query('brand')->create($request->validated())
        );
    }

    public function show(Brand $brand): JsonResource
    {
        $this->authorizeIfConfigured('view', $brand);

        return BrandResource::make($brand);
    }

    public function update(UpdateBrandRequest $request, Brand $brand): JsonResource
    {
        $this->authorizeIfConfigured('update', $brand);

        $brand->fill($request->validated());
        $brand->save();

        return BrandResource::make($brand->refresh());
    }

    public function destroy(Brand $brand)
    {
        $this->authorizeIfConfigured('delete', $brand);

        $brand->delete();

        return response()->noContent();
    }
}
