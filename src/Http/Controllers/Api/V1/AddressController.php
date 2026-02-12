<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\Address\{StoreAddressRequest, UpdateAddressRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\AddressResource;
use PictaStudio\VenditioCore\Models\Address;

use function PictaStudio\VenditioCore\Helpers\Functions\{query, resolve_model};

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

        $payload = $request->validated();
        $addressable = auth()->guard()->user();

        if ($addressable) {
            $address = $addressable->addresses()->create($payload);
        } else {
            if (!isset($payload['addressable_type'], $payload['addressable_id'])) {
                throw ValidationException::withMessages([
                    'addressable' => 'addressable_type and addressable_id are required when no authenticated user is available.',
                ]);
            }

            $address = query('address')->create($payload);
        }

        return AddressResource::make($address);
    }

    public function show(Address $address): JsonResource
    {
        $this->authorizeIfConfigured('view', $address);

        return AddressResource::make($address);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResource
    {
        $this->authorizeIfConfigured('update', $address);

        $address->fill($request->validated());
        $address->save();

        return AddressResource::make($address->refresh());
    }

    public function destroy(Address $address)
    {
        $this->authorizeIfConfigured('delete', $address);

        $address->delete();

        return response()->noContent();
    }
}
