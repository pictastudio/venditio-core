<?php

namespace PictaStudio\VenditioCore\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\{Builder, Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphTo};
use PictaStudio\VenditioCore\Enums\DiscountType;
use PictaStudio\VenditioCore\Models\Scopes\{Active, InDateRange};
use PictaStudio\VenditioCore\Models\Traits\{HasHelperMethods, LogsActivity};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Discount extends Model
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

    protected function casts(): array
    {
        return [
            'type' => DiscountType::class,
            'value' => 'decimal:2',
            'active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'apply_to_cart_total' => 'boolean',
            'apply_once_per_cart' => 'boolean',
            'max_uses_per_user' => 'integer',
            'one_per_user' => 'boolean',
            'free_shipping' => 'boolean',
            'minimum_order_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScopes([
            Active::class,
            // InDateRange::class,
        ]);
    }

    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(resolve_model('discount_application'));
    }

    #[Scope]
    public function active(Builder $query): Builder
    {
        return $query->where('active', true)
            ->where('starts_at', '<=', now());
    }

    #[Scope]
    public function inDateRange(Builder $query, CarbonInterface $startsAt, ?CarbonInterface $endsAt = null): Builder
    {
        return $query->where('starts_at', '<=', $startsAt)
            ->when(
                $endsAt,
                fn (Builder $query, CarbonInterface $endsAt) => $query->where('ends_at', '>=', $endsAt),
            );
    }
}
