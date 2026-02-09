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

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;
use function PictaStudio\VenditioCore\Helpers\Functions\query;

class AddressController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', resolve_model('address'));

        $filters = request()->all();

        return AddressResource::collection(
            $this->applyBaseFilters(query('address'), $filters, 'address')
        );
    }

    public function store(StoreAddressRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', resolve_model('address'));

        return AddressResource::make(
            AddressDto::fromArray($request->validated())->create()
        );
    }

    public function show(Address $address): JsonResource
    {
        $this->authorizeIfConfigured('view', $address);

        return AddressResource::make($address);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResource
    {
        $this->authorizeIfConfigured('update', $address);

        return AddressResource::make(
            AddressDto::fromArray(array_merge(
                $request->validated(),
                ['address' => $address]
            ))->update()
        );
    }

    public function destroy(Address $address)
    {
        $this->authorizeIfConfigured('delete', $address);

        $address->delete();

        return response()->noContent();
    }
}
