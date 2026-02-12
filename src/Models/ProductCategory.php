<?php

namespace PictaStudio\Venditio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsToMany};
use Nevadskiy\Tree\AsTree;
use PictaStudio\Venditio\Models\Scopes\{Active, Ordered};
use PictaStudio\Venditio\Models\Traits\{HasDiscounts, HasHelperMethods, LogsActivity};

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class ProductCategory extends Model
{
    use AsTree;
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

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product'), 'product_category_product')
            ->withTimestamps();
    }
}
