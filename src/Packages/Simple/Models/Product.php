<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasOne, MorphMany};
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\{Active, InDateRange};
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\{HasHelperMethods, LogsActivity};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Product extends Model
{
    use HasFactory;
    use HasHelperMethods;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => config('venditio-core.product.status_enum'),
            'active' => 'boolean',
            'new' => 'boolean',
            'in_evidence' => 'boolean',
            'visible_from' => 'datetime',
            'visible_until' => 'datetime',
            'images' => 'json',
            'files' => 'json',
            'measuring_unit' => config('venditio-core.product.measuring_unit_enum'),
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'metadata' => 'json',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Active::class,
            new InDateRange('visible_from', 'visible_until'),
        ]);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(resolve_model('brand'));
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(resolve_model('tax_class'));
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product_category'), 'product_category_product')
            ->withTimestamps();
    }

    public function discounts(): MorphMany
    {
        return $this->morphMany(resolve_model('discount'), 'discountable');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(resolve_model('inventory'));
    }
}
