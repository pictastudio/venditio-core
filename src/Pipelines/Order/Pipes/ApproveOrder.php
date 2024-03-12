<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use PictaStudio\VenditioCore\Enums\OrderStatus;
use PictaStudio\VenditioCore\Models\Order;

class ApproveOrder
{
    public function __invoke(Order $order, Closure $next): Order
    {
        $order->fill([
            'status' => OrderStatus::COMPLETED,
            'approved_at' => now(),
        ]);

        $order->save();

        return $next($order);
    }
}
