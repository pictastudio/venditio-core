<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Casts\Decimal;
use PictaStudio\VenditioCore\Enums\ProductMeasuringUnit;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\InDateRange;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'new' => 'boolean',
        'in_evidence' => 'boolean',
        'visible_from' => 'datetime',
        'visible_to' => 'datetime',
        'images' => 'array',
        'measuring_unit' => ProductMeasuringUnit::class,
        'weight' => Decimal::class,
        'length' => Decimal::class,
        'width' => Decimal::class,
        'depth' => Decimal::class,
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Active::class,
            new InDateRange('visible_from', 'visible_to'),
        ]);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.brand'));
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.product_type'));
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.tax_class'));
    }

    public function category(): BelongsToMany
    {
        return $this->belongsToMany(config('venditio-core.models.product_category'), 'product_category_product')
            ->withTimestamps();
    }

    public function discount(): MorphMany
    {
        return $this->morphMany(config('venditio-core.models.discount'), 'discountable');
    }

    public function productItems(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.product_item'));
    }
}
