<?php

namespace PictaStudio\Venditio\Support;

use Illuminate\Support\Fluent;

class AddressSnapshot
{
    private const ADDRESS_KEYS = [
        'country_id',
        'province_id',
        'first_name',
        'last_name',
        'email',
        'sex',
        'phone',
        'vat_number',
        'fiscal_code',
        'sdi',
        'pec',
        'company_name',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip',
        'birth_date',
        'birth_place',
        'notes',
        'country',
        'province',
    ];

    public static function collection(mixed $addresses): ?array
    {
        if ($addresses instanceof Fluent) {
            $addresses = $addresses->toArray();
        }

        if (!is_array($addresses)) {
            return null;
        }

        return collect($addresses)
            ->filter(fn (mixed $address, mixed $type): bool => is_string($type) && is_array($address))
            ->map(fn (array $address): array => self::make($address))
            ->all();
    }

    public static function make(mixed $address): array
    {
        if ($address instanceof Fluent) {
            $address = $address->toArray();
        }

        if (!is_array($address)) {
            return [];
        }

        return collect($address)
            ->only(self::ADDRESS_KEYS)
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();
    }
}
