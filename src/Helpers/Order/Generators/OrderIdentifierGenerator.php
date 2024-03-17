<?php

namespace PictaStudio\VenditioCore\Helpers\Order\Generators;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Helpers\Order\Contracts\OrderIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Models\Contracts\Order;

class OrderIdentifierGenerator implements OrderIdentifierGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(Model $order): string
    {
        $year = $order->created_at->year;
        $month = $order->created_at->format('m');

        $latestIdentifier = app(Order::class)::query()
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
                '{increment}' => mb_str_pad($increment, 6, 0, STR_PAD_LEFT),
            ])
            ->toString();
    }
}
