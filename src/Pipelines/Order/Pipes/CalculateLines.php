<?php

namespace PictaStudio\Venditio\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CalculateLines
{
    public function __invoke(Model $order, Closure $next): Model
    {
        $lines = $order->getRelation('lines');
        $sourceCart = $order->getRelation('sourceCart');

        if (!$lines instanceof Collection) {
            $lines = collect($lines ?? []);
        }

        $subTotalTaxable = $lines->sum('unit_final_price_taxable');
        $subTotalTax = $lines->sum('unit_final_price_tax');
        $subTotal = $lines->sum('total_final_price');
        $discountAmount = (float) ($order->discount_amount ?? 0);
        $totalFinal = max(0, $subTotal + (float) $order->shipping_fee + (float) $order->payment_fee - $discountAmount);

        $order->fill([
            'sub_total_taxable' => $subTotalTaxable,
            'sub_total_tax' => $subTotalTax,
            'sub_total' => $subTotal,
            'discount_amount' => $discountAmount,
            'total_final' => $totalFinal,
        ]);

        $order->unsetRelation('lines');

        $order->save();

        $order->lines()->saveMany($lines);

        $order->unsetRelation('sourceCart');
        $order = $order->fresh(['lines']) ?? $order->load('lines');

        if ($sourceCart instanceof Model) {
            $order->setRelation('sourceCart', $sourceCart);
        }

        return $next($order);
    }
}
