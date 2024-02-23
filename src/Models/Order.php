<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Casts\Price;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'tracking_date' => 'datetime',
        'sub_total_taxable' => Price::class,
        'sub_total_tax' => Price::class,
        'sub_total' => Price::class,
        'shipping_fee' => Price::class,
        'payment_fee' => Price::class,
        'discount_amount' => Price::class,
        'total_final' => Price::class,
        'addresses' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.user'));
    }

    public function shippingStatus(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.shipping_status'));
    }

    public function lines(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.order_line'));
    }
}
