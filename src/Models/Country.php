<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\Address;
use PictaStudio\VenditioCore\Models\Contracts\Currency;
use PictaStudio\VenditioCore\Models\Contracts\TaxClass;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

class Country extends Model
{
    use HasFactory;
    use HasHelperMethods;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function taxClasses(): BelongsToMany
    {
        return $this->belongsToMany(app(TaxClass::class), 'country_tax_class')
            ->using(config('venditio-core.models.country_tax_class')::class)
            ->withTimestamps()
            ->withPivot('rate');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(app(Address::class));
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(app(Currency::class));
    }
}
