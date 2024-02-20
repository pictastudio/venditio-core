<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Base\Casts\Decimal;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\InDateRange;

class ProductItem extends Model
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
        'visible_from' => 'datetime',
        'visible_to' => 'datetime',
        'images' => 'array',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function discount(): MorphMany
    {
        return $this->morphMany(Discount::class, 'discountable');
    }

    public function productVariantOption(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariantOption::class, 'product_configuration');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
