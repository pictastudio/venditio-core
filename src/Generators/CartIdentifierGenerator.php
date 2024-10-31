<?php

namespace PictaStudio\VenditioCore\Generators;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\CartIdentifierGeneratorInterface;

use function PictaStudio\VenditioCore\Helpers\Functions\query;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CartIdentifierGenerator implements CartIdentifierGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(Model $cart): string
    {
        $year = $cart->created_at->year;
        $month = $cart->created_at->format('m');

        $latestIdentifier = query('cart')
            ->withoutGlobalScopes()
            ->selectRaw('MAX(identifier) as identifier')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            // ->whereNot('id', $cart->getKey())
            ->value('identifier');

        $increment = 1;

        if ($latestIdentifier) {
            $segments = explode('-', $latestIdentifier);

            if (count($segments) !== 1) {
                $increment = end($segments) + 1;
            }
        }

        while (
            query('cart')
                ->withoutGlobalScopes()
                ->where('identifier', $this->buildIdentifier($year, $month, $increment))
                ->exists()
        ) {
            $increment++;
        }

        return $this->buildIdentifier($year, $month, $increment);
    }

    private function buildIdentifier(int $year, string $month, int $increment): string
    {
        $identifierTemplate = '{year}-{month}-{increment}';

        return str($identifierTemplate)
            ->swap([
                '{year}' => $year,
                '{month}' => $month,
                '{increment}' => mb_str_pad($increment, 6, 0, STR_PAD_LEFT),
            ])
            ->toString();
    }
}
