<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

class TaxClass extends Model
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

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(config('venditio-core.models.country'), 'country_tax_class')
            ->using(config('venditio-core.models.country_tax_class'))
            ->withTimestamps()
            ->withPivot('rate');
    }
}
