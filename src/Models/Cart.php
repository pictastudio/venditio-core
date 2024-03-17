<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\CartLine;
use PictaStudio\VenditioCore\Models\Contracts\Order;
use PictaStudio\VenditioCore\Models\Contracts\User;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;

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

    protected $casts = [
        'sub_total_taxable' => 'decimal:2',
        'sub_total_tax' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_final' => 'decimal:2',
        'addresses' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(app(User::class));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(app(Order::class));
    }

    public function lines(): HasMany
    {
        return $this->hasMany(app(CartLine::class));
    }
}
