<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\Order;
use PictaStudio\VenditioCore\Models\Contracts\ProductItem;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

class OrderLine extends Model
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
        'unit_price' => 'decimal:2',
        'unit_discount' => 'decimal:2',
        'unit_final_price' => 'decimal:2',
        'unit_final_price_tax' => 'decimal:2',
        'unit_final_price_taxable' => 'decimal:2',
        'total_final_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'product_item' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(app(Order::class));
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(app(ProductItem::class));
    }
}
