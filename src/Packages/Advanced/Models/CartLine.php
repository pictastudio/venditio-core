<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class CartLine extends Model
{
    use HasFactory;
    use HasHelperMethods;
    use SoftDeletes;

    protected $guarded = [
        // 'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'unit_discount' => 'decimal:2',
            'unit_final_price' => 'decimal:2',
            'unit_final_price_tax' => 'decimal:2',
            'unit_final_price_taxable' => 'decimal:2',
            'total_final_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'product_item' => 'json',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(resolve_model('cart'));
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(resolve_model('product_item'));
    }
}
