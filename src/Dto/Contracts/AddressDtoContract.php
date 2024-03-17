<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use PictaStudio\VenditioCore\Models\Contracts\Address;

interface AddressDtoContract extends Dto
{
    public function getAddress(): Address|Model;

    public function getAddressable(): ?Model;

    public function getType(): ?string;

    public function isDefault(): bool;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getEmail(): ?string;

    public function getSex(): ?string;

    public function getPhone(): ?string;

    public function getVatNumber(): ?string;

    public function getFiscalCode(): ?string;

    public function getCompanyName(): ?string;

    public function getAddressLine1(): ?string;

    public function getAddressLine2(): ?string;

    public function getCity(): ?string;

    public function getState(): ?string;

    public function getZip(): ?string;

    public function getBirthDate(): null|string|Date;

    public function getBirthPlace(): ?string;

    public function getNotes(): ?string;
}
