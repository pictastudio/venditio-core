<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\HasMany;
use PictaStudio\VenditioCore\Packages\Simple\Models\Traits\{HasHelperMethods, LogsActivity};

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Brand extends Model
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

    public function products(): HasMany
    {
        return $this->hasMany(resolve_model('product'));
    }
}
