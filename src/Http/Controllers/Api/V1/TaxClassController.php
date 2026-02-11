<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Http\Controllers\Api\Controller;
use PictaStudio\VenditioCore\Http\Requests\V1\TaxClass\{StoreTaxClassRequest, UpdateTaxClassRequest};
use PictaStudio\VenditioCore\Http\Resources\V1\GenericModelResource;
use PictaStudio\VenditioCore\Models\TaxClass;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class TaxClassController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $includes = request()->query('include', []);

        $this->validateData([
            'include' => $includes,
        ], [
            'include' => ['array'],
            'include.*' => [
                'string',
                Rule::in(['countries']),
            ],
        ]);

        return GenericModelResource::collection(
            $this->applyBaseFilters(query('tax_class')->with($includes), request()->all(), 'tax_class')
        );
    }

    public function store(StoreTaxClassRequest $request): JsonResource
    {
        $taxClass = query('tax_class')->create($request->validated());

        return GenericModelResource::make($taxClass);
    }

    public function show(TaxClass $taxClass): JsonResource
    {
        return GenericModelResource::make($taxClass);
    }

    public function update(UpdateTaxClassRequest $request, TaxClass $taxClass): JsonResource
    {
        $taxClass->fill($request->validated());
        $taxClass->save();

        return GenericModelResource::make($taxClass->refresh());
    }

    public function destroy(TaxClass $taxClass)
    {
        $taxClass->delete();

        return response()->noContent();
    }
}
