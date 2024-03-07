<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;

class CartLine extends Model
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

    public function cart(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.cart'));
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(config('venditio-core.models.product_item'));
    }
}
