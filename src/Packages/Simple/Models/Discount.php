<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\{Builder, Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PictaStudio\VenditioCore\Packages\Simple\Enums\DiscountType;
use PictaStudio\VenditioCore\Packages\Simple\Models\Scopes\{Active, InDateRange};
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\{HasHelperMethods, LogsActivity};

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
