<?php

namespace PictaStudio\Venditio\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use PictaStudio\Venditio\Http\Controllers\Api\Controller;
use PictaStudio\Venditio\Http\Requests\V1\PaymentMethod\{StorePaymentMethodRequest, UpdatePaymentMethodRequest};
use PictaStudio\Venditio\Http\Resources\V1\PaymentMethodResource;
use PictaStudio\Venditio\Models\PaymentMethod;

use function PictaStudio\Venditio\Helpers\Functions\{query, resolve_model};

class PaymentMethodController extends Controller
{
    public function index(): JsonResource|JsonResponse
    {
        $this->authorizeIfConfigured('viewAny', resolve_model('payment_method'));

        return PaymentMethodResource::collection(
            $this->applyBaseFilters(query('payment_method'), request()->all(), 'payment_method')
        );
    }

    public function store(StorePaymentMethodRequest $request): JsonResource
    {
        $this->authorizeIfConfigured('create', resolve_model('payment_method'));

        $paymentMethod = query('payment_method')->create($request->validated());

        return PaymentMethodResource::make($paymentMethod->refresh());
    }

    public function show(PaymentMethod $paymentMethod): JsonResource
    {
        $this->authorizeIfConfigured('view', $paymentMethod);

        return PaymentMethodResource::make($paymentMethod);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): JsonResource
    {
        $this->authorizeIfConfigured('update', $paymentMethod);

        $paymentMethod->fill($request->validated());
        $paymentMethod->save();

        return PaymentMethodResource::make($paymentMethod->refresh());
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorizeIfConfigured('delete', $paymentMethod);

        $paymentMethod->delete();

        return response()->noContent();
    }
}
