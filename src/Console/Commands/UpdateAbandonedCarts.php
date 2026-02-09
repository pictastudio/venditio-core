<?php

namespace PictaStudio\VenditioCore\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Models\Cart;

use function PictaStudio\VenditioCore\Helpers\Functions\query;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_dto;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_enum;

class UpdateAbandonedCarts extends Command
{
    protected $signature = 'carts:update-abandoned';

    protected $description = 'Check for abandoned carts and update their status';

    public function handle(): void
    {
        logger()->info('[UpdateAbandonedCarts] Checking for abandoned carts...');

        query('cart')
            ->pending()
            ->where('updated_at', '<=', now()->subDays(7))
            ->chunkById(
                100,
                fn (Collection $carts) => $carts->each(
                    fn (Cart $cart) => $cart->purge()
                )
            );

        logger()->info('[UpdateAbandonedCarts] Abandoned carts updated successfully!');
    }
}
