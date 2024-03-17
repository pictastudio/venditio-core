<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\Product;
use PictaStudio\VenditioCore\Models\Contracts\ProductCategory as ProductCategoryContract;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\Ordered;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;

class ProductCategory extends Model
{
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
        return $this->belongsTo(app(ProductCategoryContract::class), 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(app(ProductCategoryContract::class), 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(app(Product::class), 'product_category_product')
            ->withTimestamps();
    }
}
