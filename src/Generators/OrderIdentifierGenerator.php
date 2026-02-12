<?php

namespace PictaStudio\Venditio\Generators;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Contracts\OrderIdentifierGeneratorInterface;

use function PictaStudio\Venditio\Helpers\Functions\{query};

class OrderIdentifierGenerator implements OrderIdentifierGeneratorInterface
{
    public function generate(Model $order): string
    {
        $year = $order->created_at->year;
        $month = $order->created_at->format('m');

        $latestIdentifier = query('order')
            ->withoutGlobalScopes()
            ->selectRaw('MAX(identifier) as identifier')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->value('identifier');

        $increment = 1;

        if ($latestIdentifier) {
            $segments = explode('-', $latestIdentifier);

            if (count($segments) !== 1) {
                $increment = end($segments) + 1;
            }
        }

        while (
            query('order')
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
