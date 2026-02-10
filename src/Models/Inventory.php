<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PictaStudio\VenditioCore\Models\Traits\{HasHelperMethods, LogsActivity};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Inventory extends Model
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
            'price' => 'decimal:2',
            'price_includes_tax' => 'boolean',
            'purchase_price' => 'decimal:2',
        ];
    }

    // protected function stockAvailable(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn (int $value) => (
    //             $this->getAttribute('stock') - $this->getAttribute('stock_reserved')
    //         ),
    //     );
    // }

    public function product(): BelongsTo
    {
        return $this->belongsTo(resolve_model('product'));
    }
}
