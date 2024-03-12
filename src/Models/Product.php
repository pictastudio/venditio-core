<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Enums\ProductMeasuringUnit;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\InDateRange;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;

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

    protected $casts = [
        'active' => 'boolean',
        'new' => 'boolean',
        'in_evidence' => 'boolean',
        'visible_from' => 'datetime',
        'visible_to' => 'datetime',
        'images' => 'array',
        // 'measuring_unit' => ProductMeasuringUnit::class,
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'depth' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeCasts([
            'status' => config('venditio-core.products.status_enum'),
        ]);
    }

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

    public function categories(): BelongsToMany
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
