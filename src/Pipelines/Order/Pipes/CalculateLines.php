<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use PictaStudio\VenditioCore\Models\Order;

class CalculateLines
{
    public function __invoke(Order $order, Closure $next): Order
    {
        $lines = $order->getRelation('lines');
        $order->unsetRelation('lines');

        $order->save();

        $order->lines()->saveMany($lines);

        return $next($order->refresh());
    }
}
