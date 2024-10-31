<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\Active;
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\InDateRange;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class ProductItem extends Model
{
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

    protected $casts = [
        'active' => 'boolean',
        'visible_from' => 'datetime',
        'visible_until' => 'datetime',
        'images' => 'json',
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
            'status' => config('venditio-core.products.status_enum'),
        ]);
    }

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Active::class,
            new InDateRange('visible_from', 'visible_until'),
        ]);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(resolve_model('product'));
    }

    public function discount(): MorphMany
    {
        return $this->morphMany(resolve_model('discount'), 'discountable');
    }

    public function productVariantOption(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product_variant'), 'product_configuration');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(resolve_model('inventory'));
    }
}
