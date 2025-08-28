<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract;
use PictaStudio\VenditioCore\Dto\OrderDto;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;

use function PictaStudio\VenditioCore\Helpers\Functions\{get_fresh_model_instance, query};

class FillOrderFromCart
{
    public function __invoke(OrderDtoContract $orderDto, Closure $next): Model
    {
        // dd($orderDto->toModel()->toArray());

        // $cart = query('cart')
        //     ->where('status', config('venditio-core.cart.status_enum')::getActiveStatus())
        //     ->findOrFail($requestValidated['cart_id']);

        // throw_if(
        //     is_null($cart),
        //     new Exception('Cart not found')
        // );

        // $order = get_fresh_model_instance('order');

        // // move this inside the OrderDto toArray method
        // $order->fill([
        //     'user_id' => auth()->id(),
        //     'status' => config('venditio-core.order.status_enum')::getProcessingStatus(),
        //     // 'tracking_code' => null,
        //     // 'tracking_date' => null,
        //     // 'courier_code' => null,
        //     'sub_total_taxable' => $cart->sub_total_taxable,
        //     'sub_total_tax' => $cart->sub_total_tax,
        //     'sub_total' => $cart->sub_total,
        //     'shipping_fee' => $cart->shipping_fee,
        //     'payment_fee' => $cart->payment_fee,
        //     'discount_code' => $cart->discount_code,
        //     'discount_amount' => $cart->discount_amount,
        //     'total_final' => $cart->total_final,
        //     'user_first_name' => $cart->user_first_name,
        //     'user_last_name' => $cart->user_last_name,
        //     'user_email' => $cart->user_email,
        //     'addresses' => $cart->addresses,
        //     'customer_notes' => $cart->notes,
        //     // 'admin_notes' => null,
        // ]);

        $order = $orderDto->toModel();

        $order->setRelation('lines', $this->mapCartLineToOrderLine($orderDto->getCart()));

        dd($order->toArray());

        return $next($order);
    }

    public function mapCartLineToOrderLine(Cart|Model $cart): Collection
    {
        return $cart->lines->map(function (Model $cartLine) {
            $orderLine = get_fresh_model_instance('order_line');

            return $orderLine->fill([
                'product_item_id' => $cartLine->product_item_id,
                'product_name' => $cartLine->product_name,
                'product_sku' => $cartLine->product_sku,
                'unit_price' => $cartLine->unit_price,
                'unit_discount' => $cartLine->unit_discount,
                'unit_final_price' => $cartLine->unit_final_price,
                'unit_final_price_tax' => $cartLine->unit_final_price_tax,
                'unit_final_price_taxable' => $cartLine->unit_final_price_taxable,
                'qty' => $cartLine->qty,
                'total_final_price' => $cartLine->total_final_price,
                'tax_rate' => $cartLine->tax_rate,
                'product_item' => $cartLine->product_item,
            ]);
        });
    }
}
