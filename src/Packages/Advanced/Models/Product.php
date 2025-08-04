<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Enums\ProductMeasuringUnit;
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\Active;
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\InDateRange;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\LogsActivity;

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

    protected $casts = [
        'active' => 'boolean',
        'new' => 'boolean',
        'in_evidence' => 'boolean',
        'visible_from' => 'datetime',
        'visible_until' => 'datetime',
        'images' => 'json',
        'files' => 'json',
        // 'measuring_unit' => ProductMeasuringUnit::class,
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'metadata' => 'json',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeCasts([
            'status' => config('venditio-core.product.status_enum'),
        ]);
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product_category'), 'product_category_product')
            ->withTimestamps();
    }

    public function discount(): MorphMany
    {
        return $this->morphMany(resolve_model('discount'), 'discountable');
    }

    public function productItems(): HasMany
    {
        return $this->hasMany(resolve_model('product_item'));
    }
}
