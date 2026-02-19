<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\Inventory\{StoreInventoryRequest, UpdateInventoryRequest};
use PictaStudio\Venditio\Http\Resources\V1\InventoryResource;
use PictaStudio\Venditio\Models\Inventory;

use function PictaStudio\Venditio\Helpers\Functions\query;

class InventoryController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', Inventory::class);

        return InventoryResource::collection(
            $this->applyBaseFilters(query('inventory'), request()->all(), 'inventory')
        );
    }

    public function store(StoreInventoryRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', Inventory::class);

        $inventory = query('inventory')->create($request->validated());

        return InventoryResource::make($inventory);
    }

    public function show(Inventory $inventory): JsonResource
    {
        $this->authorizeIfConfigured('view', $inventory);

        return InventoryResource::make($inventory);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): JsonResource
    {
        $this->authorizeIfConfigured('update', $inventory);

        $inventory->fill($request->validated());
        $inventory->save();

        return InventoryResource::make($inventory->refresh());
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorizeIfConfigured('delete', $inventory);

        $inventory->delete();

        return response()->noContent();
    }
}
