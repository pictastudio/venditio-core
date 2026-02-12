<?php

namespace PictaStudio\Venditio\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity {
        SpatieLogsActivity::bootLogsActivity as spatieBootLogsActivity;
    }

    protected static function bootLogsActivity(): void
    {
        if (!config('venditio.activity_log.enabled')) {
            return;
        }

        static::spatieBootLogsActivity();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(config('venditio.activity_log.log_name'))
            ->logAll()
            ->dontSubmitEmptyLogs()
            ->logExcept(config('venditio.activity_log.log_except'));
    }
}
