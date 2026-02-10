<?php

namespace PictaStudio\VenditioCore\Events;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Models\Product;

class ProductStockBelowMinimum
{
    public readonly Product $product;

    public readonly int $stock;

    public readonly int $stockReserved;

    public readonly int $stockAvailable;

    public readonly ?int $stockMin;

    public function __construct(
        public readonly Model $inventory,
    ) {
        $this->product = $inventory->product;
        $this->stock = (int) $inventory->stock;
        $this->stockReserved = (int) $inventory->stock_reserved;
        $this->stockAvailable = (int) $inventory->stock_available;
        $this->stockMin = $inventory->stock_min !== null ? (int) $inventory->stock_min : null;
    }
}
