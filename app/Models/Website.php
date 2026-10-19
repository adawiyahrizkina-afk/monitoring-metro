<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = [
        'nama_website',
        'instansi',
        'url',
        'status',
        'response_time',
        'monitoring_aktif',
        'last_checked_at',
    ];

    protected $casts = [
        'monitoring_aktif' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function getFaviconUrlAttribute(): ?string
    {
        $parts = parse_url($this->url);
        if (! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return $parts['scheme'] . '://' . $parts['host'] . $port . '/favicon.ico';
    }

    public function monitoringLogs()
    {
        return $this->hasMany(
            MonitoringLog::class
        );
    }
}