<?php

namespace PictaStudio\VenditioCore\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('venditio-core')
            ->logAll()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at']);
    }
}
