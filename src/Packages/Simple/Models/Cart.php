<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Fluent;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\LogsActivity;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_enum;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Cart extends Model
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
            'status' => config('venditio-core.cart.status_enum'),
            'sub_total_taxable' => 'decimal:2',
            'sub_total_tax' => 'decimal:2',
            'sub_total' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'payment_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_final' => 'decimal:2',
            'addresses' => 'json',
        ];
    }

    protected function addresses(): Attribute
    {
        return Attribute::make(
            get: fn (array $value) => new Fluent($value),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(resolve_model('user'));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(resolve_model('order'));
    }

    public function lines(): HasMany
    {
        return $this->hasMany(resolve_model('cart_line'));
    }

    #[Scope]
    public function processing(Builder $builder): Builder
    {
        return $builder->where('status', resolve_enum('cart_status')::getProcessingStatus());
    }

    #[Scope]
    public function active(Builder $builder): Builder
    {
        return $builder->where('status', resolve_enum('cart_status')::getActiveStatus());
    }

    #[Scope]
    public function converted(Builder $builder): Builder
    {
        return $builder->where('status', resolve_enum('cart_status')::getConvertedStatus());
    }

    #[Scope]
    public function cancelled(Builder $builder): Builder
    {
        return $builder->where('status', resolve_enum('cart_status')::getCancelledStatus());
    }

    #[Scope]
    public function abandoned(Builder $builder): Builder
    {
        return $builder->where('status', resolve_enum('cart_status')::getAbandonedStatus());
    }

    #[Scope]
    public function pending(Builder $builder): Builder
    {
        return $builder->whereIn('status', resolve_enum('cart_status')::getPendingStatuses());
    }

    #[Scope]
    public function completed(Builder $builder): Builder
    {
        return $builder->whereIn('status', resolve_enum('cart_status')::getCompletedStatuses());
    }

    #[Scope]
    public function inactive(Builder $builder): Builder
    {
        return $builder->whereIn('status', resolve_enum('cart_status')::getInactiveStatuses());
    }

    public function purge(): void
    {
        $this->lines()->delete();
        $this->update([
            'status' => resolve_enum('cart_status')::getCancelledStatus(),
        ]);
        $this->delete();
    }
}
