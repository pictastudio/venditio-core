<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\OrderLine;
use PictaStudio\VenditioCore\Models\Contracts\ShippingStatus;
use PictaStudio\VenditioCore\Models\Contracts\User;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;

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

    protected $casts = [
        'tracking_date' => 'datetime',
        'sub_total_taxable' => 'decimal:2',
        'sub_total_tax' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_final' => 'decimal:2',
        'addresses' => 'array',
        'approved_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeCasts([
            'status' => config('venditio-core.orders.status_enum'),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(app(User::class));
    }

    public function shippingStatus(): BelongsTo
    {
        return $this->belongsTo(app(ShippingStatus::class));
    }

    public function lines(): HasMany
    {
        return $this->hasMany(app(OrderLine::class));
    }
}
