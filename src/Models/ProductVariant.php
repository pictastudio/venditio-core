<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\ProductType;
use PictaStudio\VenditioCore\Models\Contracts\ProductVariantOption;
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
        return $this->belongsTo(app(ProductType::class));
    }

    public function productVariantOptions(): HasMany
    {
        return $this->hasMany(app(ProductVariantOption::class));
    }
}
