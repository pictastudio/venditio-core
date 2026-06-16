<?php

namespace PictaStudio\Venditio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\HasMany;
use PictaStudio\Venditio\Models\Traits\HasHelperMethods;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class PaymentMethod extends Model
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

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'flat_fee' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $paymentMethod): void {
            $paymentMethod->orders()->withoutGlobalScopes()->update(['payment_method_id' => null]);
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(resolve_model('order'));
    }
}
