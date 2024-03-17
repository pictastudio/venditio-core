<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use PictaStudio\VenditioCore\Models\Contracts\Country;
use PictaStudio\VenditioCore\Models\Contracts\TaxClass;

class CountryTaxClass extends Pivot
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(app(Country::class));
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(app(TaxClass::class));
    }
}
