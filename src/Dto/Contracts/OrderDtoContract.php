<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Models\Contracts\Cart;
use PictaStudio\VenditioCore\Models\Contracts\Order;

interface OrderDtoContract extends Dto
{
    public static function fromCart(Model $cart): static;

    public static function fromArray(array $data): static;

    public function getOrder(): Order|Model;

    public function getCart(): Cart|Model;

    public function getUserId(): ?int;

    public function getUserFirstName(): ?string;

    public function getUserLastName(): ?string;

    public function getUserEmail(): ?string;

    public function getDiscountRef(): ?string;

    public function getBillingAddress(): ?array;

    public function getShippingAddress(): ?array;

    public function getCustomerNotes(): ?string;

    public function getLines(): Collection;

    public static function getInstance(): Model;
}
