<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\Discount;
use PictaStudio\VenditioCore\Models\Contracts\Inventory;
use PictaStudio\VenditioCore\Models\Contracts\Product;
use PictaStudio\VenditioCore\Models\Contracts\ProductVariant;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\InDateRange;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

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
        'visible_to' => 'datetime',
        'images' => 'array',
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

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(app(Product::class));
    }

    public function discount(): MorphMany
    {
        return $this->morphMany(app(Discount::class), 'discountable');
    }

    public function productVariantOption(): BelongsToMany
    {
        return $this->belongsToMany(app(ProductVariant::class), 'product_configuration');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(app(Inventory::class));
    }
}
