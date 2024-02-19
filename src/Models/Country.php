<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function taxClasses(): BelongsToMany
    {
        return $this->belongsToMany(TaxClass::class, 'country_tax_class')
            ->using(CountryTaxClass::class)
            ->withTimestamps()
            ->withPivot('rate');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }
}
