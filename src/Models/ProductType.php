<?php

namespace PictaStudio\Venditio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\HasMany;
use PictaStudio\Translatable\Contracts\Translatable as TranslatableContract;
use PictaStudio\Venditio\Models\Scopes\Active;
use PictaStudio\Venditio\Models\Traits\{EnsuresSlug, HasDiscounts, HasHelperMethods, ResolvesRouteBindingByIdOrSlug, SyncsTranslatedSlugs, VenditioTranslatable};
use Spatie\Sluggable\{HasSlug, SlugOptions};

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class ProductType extends Model implements TranslatableContract
{
    use EnsuresSlug;
    use HasDiscounts;
    use HasFactory;
    use HasHelperMethods;
    use HasSlug;
    use ResolvesRouteBindingByIdOrSlug;
    use SoftDeletes;
    use SyncsTranslatedSlugs;
    use VenditioTranslatable;

    public array $translatedAttributes = ['name', 'slug'];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(Active::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(resolve_model('product'));
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(resolve_model('product_variant'));
    }

    public function productCustomFields(): HasMany
    {
        return $this->hasMany(resolve_model('product_custom_field'));
    }

    public function tags(): HasMany
    {
        return $this->hasMany(resolve_model('tag'));
    }

    public function getSlugOptions(): SlugOptions
    {
        return $this->venditioSlugOptions(
            SlugOptions::create()
                ->generateSlugsFrom('name')
                ->saveSlugsTo('slug'),
            'product_type',
        );
    }
}
