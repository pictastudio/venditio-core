<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Models\Order;

class ApproveOrder
{
    public function __invoke(Order $order, Closure $next): Model
    {
        $order->fill([
            'status' => config('venditio-core.orders.status_enum')::getCompletedStatus(),
            'approved_at' => now(),
        ]);

        $order->save();

        return $next($order);
    }
}
