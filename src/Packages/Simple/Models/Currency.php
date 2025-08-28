<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\{HasDefault, HasHelperMethods};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Currency extends Model
{
    use HasDefault;
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
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(resolve_model('country'));
    }
}
