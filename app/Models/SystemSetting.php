<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    public const WEBSITE_LIST_INTERVAL_KEY = 'website_list_check_interval_seconds';
    public const MONITORING_INTERVAL_KEY = 'monitoring_check_interval_seconds';
    public const DEFAULT_WEBSITE_LIST_INTERVAL = 60;
    public const DEFAULT_MONITORING_INTERVAL = 30;
    public const WEBSITE_LIST_INTERVAL_OPTIONS = [30, 60, 120, 300];
    public const MONITORING_INTERVAL_OPTIONS = [15, 30, 60, 120];

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, string $default): string
    {
        return (string) (static::where('key', $key)->value('value') ?? $default);
    }

    public static function setValue(string $key, string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
