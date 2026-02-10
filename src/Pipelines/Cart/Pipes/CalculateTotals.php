<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CalculateTotals
{
    public function __invoke(Model $cart, Closure $next): Model
    {
        $lines = $cart->getRelation('lines');

        if (!$lines instanceof Collection) {
            $lines = collect($lines);
        }

        $cart->unsetRelation('lines');

        $subTotalTaxable = $lines->sum('unit_final_price_taxable');
        $subTotalTax = $lines->sum('unit_final_price_tax');
        $subTotal = $lines->sum('total_final_price');
        $totalFinal = max(0, $subTotal + $cart->shipping_fee + $cart->payment_fee - $cart->discount_amount);

        $cart->fill([
            'status' => config('venditio-core.cart.status_enum')::getActiveStatus(),
            'sub_total_taxable' => $subTotalTaxable,
            'sub_total_tax' => $subTotalTax,
            'sub_total' => $subTotal,
            'total_final' => $totalFinal,
        ]);

        $cart->save();

        $cart->lines()->saveMany($lines);

        return $next($cart->refresh());
    }
}
