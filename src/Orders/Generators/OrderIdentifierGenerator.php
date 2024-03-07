<?php

namespace PictaStudio\VenditioCore\Orders\Generators;

use PictaStudio\VenditioCore\Models\Order;
use PictaStudio\VenditioCore\Orders\Contracts\OrderIdentifierGeneratorInterface;

class OrderIdentifierGenerator implements OrderIdentifierGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(Order $order): string
    {
        $year = $order->created_at->year;

        $month = $order->created_at->format('m');

        $latest = Order::query()
            ->selectRaw('MAX(identifier) as identifier')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNot('id', $order->getKey())
            ->first();

        if (!$latest || !$latest->identifier) {
            $increment = 1;
        } else {
            $segments = explode('-', $latest->identifier);

            if (count($segments) == 1) {
                $increment = 1;
            } else {
                $increment = end($segments) + 1;
            }
        }

        $identifierTemplate = '{year}-{month}-{increment}';

        return str($identifierTemplate)
            ->swap([
                'year' => $year,
                'month' => $month,
                'increment' => str_pad($increment, 4, 0, STR_PAD_LEFT),
            ])
            ->toString();
    }
}
