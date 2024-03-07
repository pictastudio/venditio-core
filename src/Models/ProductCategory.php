<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\Ordered;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

class ProductCategory extends Model
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
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Ordered::class,
            Active::class,
        ]);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.product_category'), 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.product_category'), 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(config('venditio-core.models.product'), 'product_category_product')
            ->withTimestamps();
    }
}
