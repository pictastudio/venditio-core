<?php

namespace PictaStudio\Venditio\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Models\Order;

class ApproveOrder
{
    public function __invoke(Order $order, Closure $next): Model
    {
        $order->fill([
            'status' => config('venditio.order.status_enum')::getCompletedStatus(),
            'approved_at' => now(),
        ]);

        $order->save();

        return $next($order);
    }
}
