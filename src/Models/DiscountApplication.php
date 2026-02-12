<?php

namespace PictaStudio\Venditio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use PictaStudio\Venditio\Models\Traits\HasHelperMethods;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class DiscountApplication extends Model
{
    use HasFactory;
    use HasHelperMethods;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'amount' => 'decimal:2',
        ];
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(resolve_model('discount'));
    }

    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(resolve_model('order'));
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(resolve_model('cart'));
    }
}
