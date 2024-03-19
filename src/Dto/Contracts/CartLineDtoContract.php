<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Models\Contracts\Cart;
use PictaStudio\VenditioCore\Models\Contracts\CartLine;

interface CartLineDtoContract extends Dto
{
    public static function fromArray(array $data): static;

    public function getCart(): Cart|Model;

    public function getCartLine(): CartLine|Model;

    public function getProductItemId(): ?int;

    public function getQty(): int;
}
