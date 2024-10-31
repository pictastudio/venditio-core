<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;
use PictaStudio\VenditioCore\Packages\Simple\Models\CartLine;

interface CartLineDtoContract extends Dto
{
    public static function fromArray(array $data): static;

    public function getCart(): Cart|Model;

    public function getCartLine(): CartLine|Model;

    public function getProductId(): ?int;

    public function getQty(): int;
}
