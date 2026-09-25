<?php

namespace App\Services;

use App\Models\MonitoringLog;
use App\Models\SystemSetting;
use Carbon\Carbon;

class MonitoringLogCleanup
{
    public const RETENTION_SETTING = 'monitoring_log_retention_days';
    public const DEFAULT_RETENTION_DAYS = 30;

    public function handle(): int
    {
        $days = (int) SystemSetting::getValue(
            self::RETENTION_SETTING,
            (string) self::DEFAULT_RETENTION_DAYS
        );

        if (! in_array($days, [1, 7, 30, 365], true)) {
            $days = self::DEFAULT_RETENTION_DAYS;
        }

        return MonitoringLog::where(
            'checked_at',
            '<',
            Carbon::now()->subDays($days)
        )->delete();
    }
}
