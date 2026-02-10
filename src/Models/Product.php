<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne};
use PictaStudio\VenditioCore\Models\Scopes\{Active, InDateRange};
use PictaStudio\VenditioCore\Models\Traits\{HasDiscounts, HasHelperMethods, LogsActivity};
use Spatie\Sluggable\{HasSlug, SlugOptions};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Product extends Model
{
    use HasDiscounts;
    use HasFactory;
    use HasHelperMethods;
    use HasSlug;
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
            'qty_for_unit' => 'integer',
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

    public function productType(): BelongsTo
    {
        return $this->belongsTo(resolve_model('product_type'));
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(resolve_model('tax_class'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product_category'), 'product_category_product')
            ->withTimestamps();
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(resolve_model('inventory'));
    }

    public function variantOptions(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product_variant_option'), 'product_configuration')
            ->withTimestamps();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
