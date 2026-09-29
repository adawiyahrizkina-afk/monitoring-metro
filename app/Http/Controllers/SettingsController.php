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

        return view('dashboard.admin.pengaturan.index', compact('retentionDays'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'retention_days' => ['required', 'integer', 'in:0,1,7,30,365'],
        ]);

        SystemSetting::setValue(
            MonitoringLogCleanup::RETENTION_SETTING,
            (string) $data['retention_days']
        );

        app(MonitoringLogCleanup::class)->handle();

        return redirect()
            ->route('pengaturan.index')
            ->with('success', 'Pengaturan lama riwayat berhasil disimpan.');
    }
}
