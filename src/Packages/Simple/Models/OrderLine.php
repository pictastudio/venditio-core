<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

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
        'product_data' => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(resolve_model('order'));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(resolve_model('product'));
    }
}
