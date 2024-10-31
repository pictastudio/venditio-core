<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use BackedEnum;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Pipelines\CartLine\CartLineCreationPipeline;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;

class FillUserDetails
{
    public function __invoke(CartDtoContract $cartDto, Closure $next): Model
    {
        // $requestValidated = array_filter(
        //     $requestValidated,
        //     fn ($value) => filled($value)
        // );

        $cart = get_fresh_model_instance('cart');

        $addresses = $cartDto->getAddresses();

        if (blank($addresses)) {
            /** @var BackedEnum $addressTypeEnum */
            $addressTypeEnum = config('venditio-core.addresses.type_enum');

            collect($addressTypeEnum::cases())
                ->each(function ($case) use (&$addresses) {
                    $addresses[$case->value] = [];
                });
        }

        $cart->fill(array_merge(
            $cartDto->toArray(),
            [
                'status' => config('venditio-core.carts.status_enum')::getProcessingStatus(),
                'addresses' => $addresses,
            ]
        ));

        // $cart->fill(array_merge(
        //     $requestValidated,
        //     [
        //         'status' => config('venditio-core.carts.status_enum')::getProcessingStatus(),
        //         'addresses' => $addresses,
        //     ]
        // ));


        // $cart = $cartDto->getCart()->updateTimestamps();

        // $billing = $cartDto->getBillingAddress();
        // $shipping = $cartDto->getShippingAddress();

        // $data = [
        //     'user_id' => $cartDto->getUserId(),
        //     'user_first_name' => $cartDto->getUserFirstName(),
        //     'user_last_name' => $cartDto->getUserLastName(),
        //     'user_email' => $cartDto->getUserEmail(),
        //     'discount_code' => $cartDto->getDiscountRef(),
        //     'addresses' => [],
        // ];

        // if (filled($billing)) {
        //     $data['addresses']['billing'] = $billing;
        // }

        // if (filled($shipping)) {
        //     $data['addresses']['shipping'] = $shipping;
        // }

        // $filteredData = collect($data)->filter(fn ($value) => filled($value));

        // $cart->fill($filteredData->toArray());

        // dd(
        //     $this->calculateLines(
        //         Arr::pull($requestValidated, 'lines', [])
        //     )
        // );

        // $cart->setRelation(
        //     'lines',
        //     $this->calculateLines(
        //         Arr::pull($requestValidated, 'lines', [])
        //     )
        // );

        $cart->setRelation('lines', $cartDto->getLines());

        return $next($cart);
    }

    // public function calculateLines(array $lines)
    // {
    //     $finalLines = [];
    //     foreach ($lines as $key => $line) {
    //         $finalLines[] = CartLineCreationPipeline::make()->run(
    //             // app(CartLineDtoContract::class)::fromArray([
    //             //     'cart' => $cart,
    //             //     'product_item_id' => $line['product_item_id'],
    //             //     'qty' => $line['qty'],
    //             // ])
    //             $line
    //         );
    //     }

    //     return $finalLines;
    // }
}
