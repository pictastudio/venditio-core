<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Dto\AddressDto;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Address\{StoreAddressRequest, UpdateAddressRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\AddressResource;
use PictaStudio\VenditioCore\Packages\Simple\Models\Address;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class AddressController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorize('viewAny', Address::class);

        $filters = request()->all();

        // $this->validateData($filters, [
        //     'all' => [
        //         'boolean',
        //     ],
        //     'id' => [
        //         'array',
        //     ],
        //     'id.*' => [
        //         Rule::exists('addresses', 'id'),
        //     ],
        // ]);

        return AddressResource::collection(
            $this->applyBaseFilters(query('address'), $filters, 'address')
        );
    }

    public function store(StoreAddressRequest $request): JsonResource
    {
        $this->authorize('create', Address::class);

        return AddressResource::make(
            AddressDto::fromArray($request->validated())->create()
        );
    }

    public function show(Address $address): JsonResource
    {
        $this->authorize('view', $address);

        return AddressResource::make($address);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResource
    {
        $this->authorize('update', $address);

        return AddressResource::make(
            AddressDto::fromArray(array_merge(
                $request->validated(),
                ['address' => $address]
            ))->update()
        );
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return response()->noContent();
    }
}
