<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CalculateLines
{
    public function __invoke(Model $order, Closure $next): Model
    {
        $lines = $order->getRelation('lines');
        $order->unsetRelation('lines');

        $order->save();

        $order->lines()->saveMany($lines);

        return $next($order->refresh());
    }
}
