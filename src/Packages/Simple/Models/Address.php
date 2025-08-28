<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\{HasDefault, HasHelperMethods, LogsActivity};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Address extends Model
{
    use HasDefault;
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
            'type' => config('venditio-core.addresses.type_enum'),
            'is_default' => 'boolean',
        ];
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(resolve_model('country'));
    }
}
