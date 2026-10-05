<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\MonitoringLogCleanup;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $retentionValue = SystemSetting::getValue(
            MonitoringLogCleanup::RETENTION_SETTING,
            (string) MonitoringLogCleanup::DEFAULT_RETENTION_DAYS
        );
        $retentionDays = in_array($retentionValue, ['0', '1', '7', '30', '365'], true)
            ? (int) $retentionValue
            : MonitoringLogCleanup::DEFAULT_RETENTION_DAYS;
        $websiteListIntervalSeconds = (int) SystemSetting::getValue(
            SystemSetting::WEBSITE_LIST_INTERVAL_KEY,
            (string) SystemSetting::DEFAULT_WEBSITE_LIST_INTERVAL
        );
        $monitoringIntervalSeconds = (int) SystemSetting::getValue(
            SystemSetting::MONITORING_INTERVAL_KEY,
            (string) SystemSetting::DEFAULT_MONITORING_INTERVAL
        );

        if (! in_array($websiteListIntervalSeconds, SystemSetting::WEBSITE_LIST_INTERVAL_OPTIONS, true)) {
            $websiteListIntervalSeconds = SystemSetting::DEFAULT_WEBSITE_LIST_INTERVAL;
        }

        if (! in_array($monitoringIntervalSeconds, SystemSetting::MONITORING_INTERVAL_OPTIONS, true)) {
            $monitoringIntervalSeconds = SystemSetting::DEFAULT_MONITORING_INTERVAL;
        }

        return view('dashboard.admin.pengaturan.index', compact(
            'retentionDays',
            'websiteListIntervalSeconds',
            'monitoringIntervalSeconds'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'retention_days' => ['required', 'integer', 'in:0,1,7,30,365'],
            'website_list_interval_seconds' => [
                'required',
                'integer',
                'in:' . implode(',', SystemSetting::WEBSITE_LIST_INTERVAL_OPTIONS),
            ],
            'monitoring_interval_seconds' => [
                'required',
                'integer',
                'in:' . implode(',', SystemSetting::MONITORING_INTERVAL_OPTIONS),
            ],
        ]);

        SystemSetting::setValue(
            MonitoringLogCleanup::RETENTION_SETTING,
            (string) $data['retention_days']
        );
        SystemSetting::setValue(
            SystemSetting::WEBSITE_LIST_INTERVAL_KEY,
            (string) $data['website_list_interval_seconds']
        );
        SystemSetting::setValue(
            SystemSetting::MONITORING_INTERVAL_KEY,
            (string) $data['monitoring_interval_seconds']
        );

        app(MonitoringLogCleanup::class)->handle();

        return redirect()
            ->route('pengaturan.index')
            ->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
