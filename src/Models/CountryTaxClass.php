<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

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
        return $this->belongsTo(config('venditio-core.models.country'));
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.tax_class'));
    }
}
