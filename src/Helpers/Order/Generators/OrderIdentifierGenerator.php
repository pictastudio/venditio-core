<?php

namespace PictaStudio\VenditioCore\Helpers\Order\Generators;

use PictaStudio\VenditioCore\Helpers\Order\Contracts\OrderIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Models\Order;

class OrderIdentifierGenerator implements OrderIdentifierGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(Order $order): string
    {
        $year = $order->created_at->year;

        $month = $order->created_at->format('m');

        $latestIdentifier = Order::query()
            ->selectRaw('MAX(identifier) as identifier')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            // ->whereNot('id', $order->getKey())
            ->value('identifier');

        $identifierTemplate = '{year}-{month}-{increment}';
        $increment = 1;

        if ($latestIdentifier) {
            $segments = explode('-', $latestIdentifier);

            if (count($segments) !== 1) {
                $increment = end($segments) + 1;
            }
        }

        return str($identifierTemplate)
            ->swap([
                '{year}' => $year,
                '{month}' => $month,
                '{increment}' => str_pad($increment, 6, 0, STR_PAD_LEFT),
            ])
            ->toString();
    }
}
