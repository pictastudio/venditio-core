<?php

namespace PictaStudio\Venditio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, MorphToMany};
use PictaStudio\Translatable\Contracts\Translatable as TranslatableContract;
use PictaStudio\Venditio\Models\Scopes\{Active, InDateRange, Ordered};
use PictaStudio\Venditio\Models\Traits\{EnsuresSlug, HasDiscounts, HasHelperMethods, HasOrderedTreeStructure, LogsActivity, ResolvesRouteBindingByIdOrSlug, SyncsTranslatedSlugs, VenditioTranslatable};
use PictaStudio\Venditio\Support\CatalogImage;
use Spatie\Sluggable\{HasSlug, SlugOptions};

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class ProductCategory extends Model implements TranslatableContract
{
    use EnsuresSlug;
    use HasDiscounts;
    use HasFactory;
    use HasHelperMethods;
    use HasOrderedTreeStructure;
    use HasSlug;
    use LogsActivity;
    use ResolvesRouteBindingByIdOrSlug;
    use SoftDeletes;
    use SyncsTranslatedSlugs;
    use VenditioTranslatable;

    public array $translatedAttributes = ['name', 'slug', 'abstract', 'description'];

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
            'show_in_menu' => 'boolean',
            'highlighted' => 'boolean',
            'visible_from' => 'datetime:Y-m-d H:i:s',
            'visible_until' => 'datetime:Y-m-d H:i:s',
            'metadata' => 'json',
            'images' => 'json',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Ordered::class,
            Active::class,
            new InDateRange('visible_from', 'visible_until'),
        ]);

        static::saving(function (self $category): void {
            if ($category->getAttribute('images') === null) {
                return;
            }

            $category->setAttribute('images', CatalogImage::normalizeCollection($category->getAttribute('images')));
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(resolve_model('product'), 'product_category_product')
            ->withTimestamps();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(resolve_model('tag'), 'taggable', 'taggables')
            ->withTimestamps();
    }

    public function getSlugOptions(): SlugOptions
    {
        return $this->venditioSlugOptions(
            SlugOptions::create()
                ->generateSlugsFrom('name')
                ->saveSlugsTo('slug'),
            'product_category',
        );
    }
}
