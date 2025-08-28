<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\Ordered;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

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
        return $this->belongsTo(resolve_model('product_type'));
    }

    public function productVariantOptions(): HasMany
    {
        return $this->hasMany(resolve_model('product_variant_option'));
    }
}
