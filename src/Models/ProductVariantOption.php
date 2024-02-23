<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Scopes\Ordered;

class ProductVariantOption extends Model
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
        'value' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(Ordered::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.product_variant'));
    }

    public function productItems(): BelongsToMany
    {
        return $this->belongsToMany(config('venditio-core.models.product_item'), 'product_configuration');
    }
}
