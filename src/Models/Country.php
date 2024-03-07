<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        return $this->belongsToMany(config('venditio-core.models.tax_class'), 'country_tax_class')
            ->using(config('venditio-core.models.country_tax_class')::class)
            ->withTimestamps()
            ->withPivot('rate');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.address'));
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.currency'));
    }
}
