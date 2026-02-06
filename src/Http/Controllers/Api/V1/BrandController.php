<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Dto\BrandDto;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Brand\StoreBrandRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\Brand\UpdateBrandRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\BrandResource;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;
use function PictaStudio\VenditioCore\Helpers\Functions\query;

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
            BrandDto::fromArray($request->validated())->create()
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

        return BrandResource::make(
            BrandDto::fromArray(array_merge(
                $request->validated(), ['brand' => $brand]
            ))->update()
        );
    }

    public function destroy(Brand $brand)
    {
        $this->authorizeIfConfigured('delete', $brand);

        $brand->delete();

        return response()->noContent();
    }
}
