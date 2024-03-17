<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Models\Contracts\Cart;

interface CartDtoContract extends Dto
{
    public static function fromArray(array $data): static;

    public function getCart(): Cart|Model;

    public function getUserId(): ?int;

    public function getUserFirstName(): ?string;

    public function getUserLastName(): ?string;

    public function getUserEmail(): ?string;

    public function getDiscountRef(): ?string;

    public function getBillingAddress(): array;

    public function getShippingAddress(): array;

    public function getLines(): Collection;

    public static function getInstance(): Model;
}
