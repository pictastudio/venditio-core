<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class ProductType extends Model
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
}
