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
        return InventoryResource::collection(
            $this->applyBaseFilters(query('inventory'), request()->all(), 'inventory')
        );
    }

    public function store(StoreInventoryRequest $request): JsonResource
    {
        $inventory = query('inventory')->create($request->validated());

        return InventoryResource::make($inventory);
    }

    public function show(Inventory $inventory): JsonResource
    {
        return InventoryResource::make($inventory);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): JsonResource
    {
        $inventory->fill($request->validated());
        $inventory->save();

        return InventoryResource::make($inventory->refresh());
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return response()->noContent();
    }
}
