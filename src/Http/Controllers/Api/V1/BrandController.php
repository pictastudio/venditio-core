<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Address\StoreAddressRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\BrandResource;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class BrandController extends Controller
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
        //         Rule::exists('brands', 'id'),
        //     ],
        // ]);

        return BrandResource::collection(
            $this->applyBaseFilters(query('brand'), $filters, 'brand')
        );
    }

    // public function store(StoreAddressRequest $request): JsonResource
    // {
    //     return BrandResource::make();
    // }

    public function show(Brand $brand): JsonResource
    {
        return BrandResource::make($brand);
    }

    // public function update(UpdateBrandRequest $request, Brand $brand): JsonResource
    // {
    //     return BrandResource::make();
    // }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return response()->noContent();
    }
}
