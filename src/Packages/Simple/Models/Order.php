<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Fluent;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\{HasHelperMethods, LogsActivity};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Order extends Model
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
            'status' => config('venditio-core.order.status_enum'),
            'last_tracked_at' => 'datetime',
            'sub_total_taxable' => 'decimal:2',
            'sub_total_tax' => 'decimal:2',
            'sub_total' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'payment_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_final' => 'decimal:2',
            'addresses' => 'json',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(resolve_model('user'));
    }

    public function shippingStatus(): BelongsTo
    {
        return $this->belongsTo(resolve_model('shipping_status'));
    }

    public function lines(): HasMany
    {
        return $this->hasMany(resolve_model('order_line'));
    }

    protected function addresses(): Attribute
    {
        return Attribute::make(
            get: fn (array $value) => new Fluent($value),
        );
    }
}
