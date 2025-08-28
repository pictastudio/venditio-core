<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

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
        return $this->belongsToMany(resolve_model('tax_class'), 'country_tax_class')
            ->using(resolve_model('country_tax_class'))
            ->withTimestamps()
            ->withPivot('rate');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(resolve_model('address'));
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(resolve_model('currency'));
    }
}
