<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Packages\Simple\Models\Contracts\Cart;
use PictaStudio\VenditioCore\Packages\Simple\Models\Contracts\Order;

interface OrderDtoContract extends Dto
{
    public static function fromCart(Model $cart): static;

    public static function fromArray(array $data): static;

    public function getModel(): Model;

    public function getCart(): Model;

    public function getUserId(): ?int;

    public function getUserFirstName(): ?string;

    public function getUserLastName(): ?string;

    public function getUserEmail(): ?string;

    public function getDiscountCode(): ?string;

    public function getBillingAddress(): ?array;

    public function getShippingAddress(): ?array;

    public function getCustomerNotes(): ?string;

    public function getLines(): Collection;

    public static function getFreshInstance(): Model;

    public function toModel(): Model;
}
