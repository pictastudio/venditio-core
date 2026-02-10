<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use PictaStudio\VenditioCore\Models\Scopes\{Active, Ordered};
use PictaStudio\VenditioCore\Models\Traits\{HasDiscounts, HasHelperMethods, LogsActivity};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class ProductCategory extends Model
{
    use HasDiscounts;
    use HasFactory;
    use HasHelperMethods;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Ordered::class,
            Active::class,
        ]);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(resolve_model('product_category'), 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(resolve_model('product_category'), 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product'), 'product_category_product')
            ->withTimestamps();
    }
}
