<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Contracts\{CartTotalDiscountCalculatorInterface, DiscountCalculatorInterface};
use PictaStudio\VenditioCore\Discounts\DiscountContext;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ApplyDiscounts
{
    public function __construct(
        private readonly DiscountCalculatorInterface $discountCalculator,
        private readonly CartTotalDiscountCalculatorInterface $cartTotalDiscountCalculator,
    ) {}

    /**
     * Called just before cart totals are calculated.
     */
    public function handle(Model $cart, Closure $next): Model
    {
        $lines = $cart->getRelation('lines');

        if (!$lines instanceof Collection) {
            $lines = collect($lines ?? []);
        }

        $context = DiscountContext::make(
            cart: $cart,
            user: $cart->relationLoaded('user')
                ? $cart->user
                : (filled($cart->getAttribute('user_id'))
                    ? query('user')->find($cart->getAttribute('user_id'))
                    : null),
        );

        $lines = $lines->map(function (Model $line) use ($context, $cart) {
            $line->setRelation('cart', $cart);
            $this->discountCalculator->apply($line, $context);
            $this->recalculateLineTotals($line);

            return $line;
        });

        $cartTotalDiscount = $this->cartTotalDiscountCalculator->resolveForTarget($cart, $lines, $context);

        $cart->setRelation('lines', $lines);
        $cart->fill([
            'discount_amount' => $cartTotalDiscount['discount_amount'],
            'discount_code' => $cartTotalDiscount['discount_code'],
        ]);

        return $next($cart);
    }

    private function recalculateLineTotals(Model $line): void
    {
        $unitFinalPrice = (float) $line->getAttribute('unit_final_price');
        $taxRate = (float) ($line->getAttribute('tax_rate') ?? 0);
        $qty = max(1, (int) ($line->getAttribute('qty') ?? 1));
        $unitFinalPriceTax = round($unitFinalPrice * ($taxRate / 100), 2);

        $line->fill([
            'unit_final_price_taxable' => $unitFinalPrice,
            'unit_final_price_tax' => $unitFinalPriceTax,
            'total_final_price' => round(($unitFinalPrice + $unitFinalPriceTax) * $qty, 2),
        ]);
    }
}
