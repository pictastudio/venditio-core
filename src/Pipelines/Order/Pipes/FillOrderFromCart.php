<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use PictaStudio\VenditioCore\Enums\OrderStatus;
use PictaStudio\VenditioCore\Models\Cart;
use PictaStudio\VenditioCore\Models\Order;

class FillOrderFromCart
{
    public static function handle(Cart $cart, Closure $next): Closure
    {
        // $cart = $order->cart->calculate();

        $order = new Order();

        $order->fill([
            'user_id' => $cart->user_id,
            'status' => OrderStatus::PENDING,
            // 'tracking_code' => null,
            // 'tracking_date' => null,
            // 'courier_code' => null,
            'sub_total_taxable' => $cart->sub_total_taxable,
            'sub_total_tax' => $cart->sub_total_tax,
            'sub_total' => $cart->sub_total,
            'shipping_fee' => $cart->shipping_fee,
            'payment_fee' => $cart->payment_fee,
            'discount_ref' => $cart->discount_ref,
            'discount_amount' => $cart->discount_amount,
            'total_final' => $cart->total_final,
            'user_first_name' => $cart->user_first_name,
            'user_last_name' => $cart->user_last_name,
            'user_email' => $cart->user_email,
            'addresses' => $cart->addresses,
            'customer_notes' => $cart->notes,
            // 'admin_notes' => null,
            // 'approved_at' => null,
        ])->save();

        return $next($order->refresh());
    }
}
