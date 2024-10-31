<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;

interface CartDtoContract extends Dto
{
    public static function fromArray(array $data): static;

    public function getCart(): Cart|Model;

    public function getUserId(): ?int;

    public function getUserFirstName(): ?string;

    public function getUserLastName(): ?string;

    public function getUserEmail(): ?string;

    public function getDiscountRef(): ?string;

    public function getAddresses(): ?array;

    public function getLines(): Collection;

    public function toModel(): Model;
}
