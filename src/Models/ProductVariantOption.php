<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\ProductItem;
use PictaStudio\VenditioCore\Models\Contracts\ProductVariant;
use PictaStudio\VenditioCore\Models\Scopes\Ordered;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

class ProductVariantOption extends Model
{
    use HasFactory;
    use HasHelperMethods;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(Ordered::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(app(ProductVariant::class));
    }

    public function productItems(): BelongsToMany
    {
        return $this->belongsToMany(app(ProductItem::class), 'product_configuration');
    }
}
