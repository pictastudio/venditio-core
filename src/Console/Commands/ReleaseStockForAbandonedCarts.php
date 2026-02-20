<?php

namespace PictaStudio\Venditio\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use PictaStudio\Venditio\Models\Cart;

use function PictaStudio\Venditio\Helpers\Functions\query;

class ReleaseStockForAbandonedCarts extends Command
{
    protected $signature = 'carts:update-abandoned';

    protected $description = 'Check for abandoned carts and update their status';

    public function handle(): int
    {
        if (!config('venditio.commands.release_stock_for_abandoned_carts.enabled', true)) {
            $this->components->info('`carts:update-abandoned` is disabled by configuration.');

            return self::SUCCESS;
        }

        $inactiveForMinutes = max(
            1,
            (int) config('venditio.commands.release_stock_for_abandoned_carts.inactive_for_minutes', 1_440)
        );
        $cutoff = now()->subMinutes($inactiveForMinutes);
        $updatedCarts = [];

        logger()->info('[ReleaseStockForAbandonedCarts] Checking for abandoned carts...');

        query('cart')
            ->pending()
            ->where('updated_at', '<=', $cutoff)
            ->chunkById(
                100,
                function (Collection $carts) use (&$updatedCarts): void {
                    $carts->each(function (Cart $cart) use (&$updatedCarts): void {
                        $cart->abandon();
                        $updatedCarts[] = $cart->identifier;
                    });
                }
            );

        logger()->info('[ReleaseStockForAbandonedCarts] Abandoned carts updated successfully. Updated carts count: ' . count($updatedCarts), ['carts' => $updatedCarts]);
        $this->components->info('Abandoned carts updated successfully. Updated carts count: ' . count($updatedCarts));

        return self::SUCCESS;
    }
}
