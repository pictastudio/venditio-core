<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Dto\OrderDto;
use PictaStudio\VenditioCore\Enums\OrderStatus;
use PictaStudio\VenditioCore\Models\Cart;
use PictaStudio\VenditioCore\Models\Order;
use PictaStudio\VenditioCore\Models\OrderLine;

class FillOrderFromCart
{
    public function __invoke(OrderDto $orderDto, Closure $next): Order
    {
        $cart = $orderDto->getCart();

        $order = $orderDto->getOrder();

        $order->fill([
            'user_id' => $orderDto->getUserId(),
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
        ]);

        $order->setRelation('lines', self::mapCartLineToOrderLine($cart));

        return $next($order);
    }

    public static function mapCartLineToOrderLine(Cart $cart): Collection
    {
        return $cart->lines->map(function ($line) {
            $orderLine = new OrderLine;

            return $orderLine->fill([
                'product_item_id' => $line->product_item_id,
                'product_name' => $line->product_name,
                'product_sku' => $line->product_sku,
                'unit_price' => $line->unit_price,
                'unit_discount' => $line->unit_discount,
                'unit_final_price' => $line->unit_final_price,
                'unit_final_price_tax' => $line->unit_final_price_tax,
                'unit_final_price_taxable' => $line->unit_final_price_taxable,
                'qty' => $line->qty,
                'total_final_price' => $line->total_final_price,
                'tax_rate' => $line->tax_rate,
                'product_item' => $line->product_item,
            ]);
        });
    }
}
