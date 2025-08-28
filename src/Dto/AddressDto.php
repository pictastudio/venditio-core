<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use PictaStudio\VenditioCore\Dto\Contracts\AddressDtoContract;
use PictaStudio\VenditioCore\Packages\Simple\Models\Address;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;

class AddressDto extends Dto implements AddressDtoContract
{
    public function __construct(
        private Model $address,
        private ?Model $addressable,
        private ?string $type,
        private bool $isDefault,
        private ?string $firstName,
        private ?string $lastName,
        private ?string $email,
        private ?string $sex,
        private ?string $phone,
        private ?string $vatNumber,
        private ?string $fiscalCode,
        private ?string $companyName,
        private ?string $addressLine1,
        private ?string $addressLine2,
        private ?string $city,
        private ?string $state,
        private ?string $zip,
        private ?string $birthDate,
        private ?string $birthPlace,
        private ?string $notes,
    ) {}

    public static function fromArray(array $data): static
    {
        $data['address'] ??= static::getFreshInstance();
        $data['addressable'] ??= auth()->user();

        return parent::fromArray($data);
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('address');
    }

    public function toModel(): Model
    {
        return $this->getFreshInstance()
            ->fill($this->toArray());
    }

    public function getAddress(): Address|Model
    {
        return $this->address;
    }

    public function getAddressable(): ?Model
    {
        return $this->addressable;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getIsDefault(): bool
    {
        return $this->default;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function getFiscalCode(): ?string
    {
        return $this->fiscalCode;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function getBirthDate(): null|string|Date
    {
        return $this->birthDate;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function create(): Model
    {
        return $this->getAddressable()
            ?->addresses()
            ?->create([
                'type' => config('venditio-core.addresses.type_enum')::tryFrom($this->getType()),
                'is_default' => $this->getIsDefault(),
                'first_name' => $this->getFirstName(),
                'last_name' => $this->getLastName(),
                'email' => $this->getEmail(),
                'sex' => $this->getSex(),
                'phone' => $this->getPhone(),
                'vat_number' => $this->getVatNumber(),
                'fiscal_code' => $this->getFiscalCode(),
                'company_name' => $this->getCompanyName(),
                'address_line_1' => $this->getAddressLine1(),
                'address_line_2' => $this->getAddressLine2(),
                'city' => $this->getCity(),
                'state' => $this->getState(),
                'zip' => $this->getZip(),
                'birth_date' => $this->getBirthDate(),
                'birth_place' => $this->getBirthPlace(),
                'notes' => $this->getNotes(),
            ]);
    }

    public function update(): Model
    {
        $updatedData = [
            // 'addressable_id' => $this->getAddressable()?->getKey(),
            // 'addressable_type' => $this->getAddressable()?->getMorphClass(),
            'type' => config('venditio-core.addresses.type_enum')::tryFrom($this->getType()),
            'is_default' => $this->getIsDefault(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'sex' => $this->getSex(),
            'phone' => $this->getPhone(),
            'vat_number' => $this->getVatNumber(),
            'fiscal_code' => $this->getFiscalCode(),
            'company_name' => $this->getCompanyName(),
            'address_line_1' => $this->getAddressLine1(),
            'address_line_2' => $this->getAddressLine2(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'zip' => $this->getZip(),
            'birth_date' => $this->getBirthDate(),
            'birth_place' => $this->getBirthPlace(),
            'notes' => $this->getNotes(),
        ];

        $updatedData = array_filter($updatedData, fn ($value) => $value !== null);

        $this->getAddress()->update($updatedData);

        return $this->getAddress()->fill($updatedData);
    }
}
