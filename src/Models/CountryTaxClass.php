<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use PictaStudio\VenditioCore\Casts\Price;

class CountryTaxClass extends Pivot
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'rate' => Price::class,
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.country'));
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.tax_class'));
    }
}
