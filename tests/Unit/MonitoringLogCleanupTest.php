<?php

namespace Tests\Unit;

use App\Models\MonitoringLog;
use App\Models\SystemSetting;
use App\Models\Website;
use App\Services\MonitoringLogCleanup;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringLogCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_zero_retention_keeps_old_monitoring_logs(): void
    {
        $log = $this->createLog(Carbon::now()->subYears(5));
        SystemSetting::setValue(MonitoringLogCleanup::RETENTION_SETTING, '0');

        $deletedCount = app(MonitoringLogCleanup::class)->handle();

        $this->assertSame(0, $deletedCount);
        $this->assertDatabaseHas('monitoring_logs', ['id' => $log->id]);
    }

    public function test_configured_retention_still_deletes_expired_logs(): void
    {
        $log = $this->createLog(Carbon::now()->subDays(31));
        SystemSetting::setValue(MonitoringLogCleanup::RETENTION_SETTING, '30');

        $deletedCount = app(MonitoringLogCleanup::class)->handle();

        $this->assertSame(1, $deletedCount);
        $this->assertDatabaseMissing('monitoring_logs', ['id' => $log->id]);
    }

    private function createLog(Carbon $checkedAt): MonitoringLog
    {
        $website = Website::create([
            'nama_website' => 'Website Tes',
            'instansi' => 'Instansi Tes',
            'url' => 'https://example.test',
        ]);

        return MonitoringLog::create([
            'website_id' => $website->id,
            'status' => 'Offline',
            'checked_at' => $checkedAt,
            'keterangan' => 'Tes',
        ]);
    }
}
