<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringLog extends Model
{
    protected $fillable = [
        'website_id',
        'status',
        'response_time',
        'checked_at',
        'keterangan',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(
            Website::class
        );
    }
}