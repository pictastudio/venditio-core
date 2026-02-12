<?php

namespace PictaStudio\VenditioCore\Generators;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\CartIdentifierGeneratorInterface;
use Illuminate\Support\Str;

use function PictaStudio\VenditioCore\Helpers\Functions\query;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CartIdentifierGenerator implements CartIdentifierGeneratorInterface
{
    public function generate(Model $cart): string
    {
        $timestamp = str(microtime(true))
            ->replace('.', '')
            ->substr(-7)
            ->toFloat();

        $random = strtoupper(Str::random(6));
        $increment = 0;

        while (
            query('cart')
                ->withoutGlobalScopes()
                ->where('identifier', $this->buildIdentifier($timestamp + $increment, $random))
                ->exists()
        ) {
            $increment++;
        }

        return $this->buildIdentifier($timestamp + $increment, $random);
    }

    private function buildIdentifier(int $timestamp, string $random): string
    {
        $identifierTemplate = '{random}-{timestamp}';

        return str($identifierTemplate)
            ->swap([
                '{timestamp}' => $timestamp,
                '{random}' => $random,
            ])
            ->toString();
    }
}
