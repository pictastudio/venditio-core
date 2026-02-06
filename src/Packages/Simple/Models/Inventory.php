<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\LogsActivity;

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
