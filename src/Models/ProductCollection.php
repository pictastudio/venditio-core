<?php

namespace PictaStudio\Venditio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, MorphToMany};
use PictaStudio\Translatable\Contracts\Translatable as TranslatableContract;
use PictaStudio\Venditio\Models\Scopes\{Active, InDateRange};
use PictaStudio\Venditio\Models\Traits\{HasDiscounts, HasHelperMethods, LogsActivity, ResolvesRouteBindingByIdOrSlug, VenditioTranslatable};
use PictaStudio\Venditio\Support\CatalogImage;
use Spatie\Sluggable\{HasSlug, SlugOptions};

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class ProductCollection extends Model implements TranslatableContract
{
    use HasDiscounts;
    use HasFactory;
    use HasHelperMethods;
    use HasSlug;
    use LogsActivity;
    use ResolvesRouteBindingByIdOrSlug;
    use SoftDeletes;
    use VenditioTranslatable;

    public array $translatedAttributes = ['name', 'slug', 'description'];

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
            'visible_from' => 'datetime:Y-m-d H:i:s',
            'visible_until' => 'datetime:Y-m-d H:i:s',
            'metadata' => 'json',
            'images' => 'json',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Active::class,
            new InDateRange('visible_from', 'visible_until'),
        ]);

        static::saving(function (self $collection): void {
            if ($collection->getAttribute('images') === null) {
                return;
            }

            $collection->setAttribute('images', CatalogImage::normalizeCollection($collection->getAttribute('images')));
        });
    }

    public function products(): BelongsToMany
    {
        $productModel = resolve_model('product');
        $product = new $productModel;

        return $this->belongsToMany($productModel, 'product_collection_product')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order')
            ->orderBy($product->qualifyColumn($product->getKeyName()));
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(resolve_model('tag'), 'taggable', 'taggables')
            ->withTimestamps();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
