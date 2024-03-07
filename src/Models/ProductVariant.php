<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Scopes\Ordered;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

class ProductVariant extends Model
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

    protected static function booted(): void
    {
        static::addGlobalScope(Ordered::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.product_type'));
    }

    public function productVariantOptions(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.product_variant_option'));
    }
}
