<?php

namespace PictaStudio\VenditioCore\Helpers\Cart\Generators;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Helpers\Cart\Contracts\CartIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Models\Contracts\Cart;

class CartIdentifierGenerator implements CartIdentifierGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(Model $cart): string
    {
        $year = $cart->created_at->year;
        $month = $cart->created_at->format('m');

        $latestIdentifier = app(Cart::class)::query()
            ->selectRaw('MAX(identifier) as identifier')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            // ->whereNot('id', $cart->getKey())
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
