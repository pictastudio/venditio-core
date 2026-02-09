<?php

namespace PictaStudio\VenditioCore\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Enums\DiscountType;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\InDateRange;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;

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
