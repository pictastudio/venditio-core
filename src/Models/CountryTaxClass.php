<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CountryTaxClass extends Pivot
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(resolve_model('country'));
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(resolve_model('tax_class'));
    }
}
