<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PictaStudio\VenditioCore\Models\Contracts\Country;
use PictaStudio\VenditioCore\Models\Traits\HasDefault;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;

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

    protected $casts = [
        'default' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeCasts([
            'type' => config('venditio-core.addresses.type_enum'),
        ]);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(app(Country::class));
    }
}
