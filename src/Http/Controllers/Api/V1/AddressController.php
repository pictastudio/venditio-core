<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Dto\AddressDto;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Address\StoreAddressRequest;
use PictaStudio\VenditioCore\Http\Requests\V1\Address\UpdateAddressRequest;
use PictaStudio\VenditioCore\Http\Resources\V1\AddressResource;
use PictaStudio\VenditioCore\Models\Address;
use PictaStudio\VenditioCore\Models\Contracts\Address as AddressContract;

class AddressController extends Controller
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

        $addresss = app(AddressContract::class)::query()
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

        return AddressResource::collection($addresss);
    }

    public function store(StoreAddressRequest $request): JsonResource
    {
        return AddressResource::make(
            AddressDto::fromArray($request->validated())->create()
        );
    }

    public function show(Address $address): JsonResource
    {
        return AddressResource::make($address);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResource
    {
        return AddressResource::make(
            AddressDto::fromArray(array_merge(
                $request->validated(),
                ['address' => $address]
            ))->update()
        );
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return response()->noContent();
    }
}
