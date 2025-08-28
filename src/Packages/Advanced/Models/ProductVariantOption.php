<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\Ordered;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

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
        'value' => 'json',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(Ordered::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(resolve_model('product_variant'));
    }

    public function productItems(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product_item'), 'product_configuration');
    }
}
