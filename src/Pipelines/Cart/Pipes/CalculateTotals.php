<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CalculateTotals
{
    public function __invoke(Model $cart, Closure $next): Model
    {
        $lines = $cart->getRelation('lines');
        $cart->unsetRelation('lines');

        $subTotalTaxable = $lines->sum('unit_final_price_taxable');
        $subTotalTax = $lines->sum('unit_final_price_tax');
        $subTotal = $lines->sum('total_final_price');
        $totalFinal = $subTotal + $cart->shipping_fee + $cart->payment_fee - $cart->discount_amount;

        $cart->fill([
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
