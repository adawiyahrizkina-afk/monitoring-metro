<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use App\Models\SystemSetting;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WebsiteController extends Controller
{
    public function dashboard()
    {
        $websites = Website::latest()->get();
        $total = $websites->count();
        $online = $websites->where('status', 'Online')->count();
        $offline = $websites->where('status', 'Offline')->count();
        $warning = $websites->where('status', 'Warning')->count();
        $unchecked = $websites->where('status', 'Belum Dicek')->count();
        $monitoringActive = $websites->where('monitoring_aktif', true)->count();
        return view('dashboard.admin.index', compact(
            'websites',
            'total',
            'online',
            'offline',
            'warning',
            'unchecked',
            'monitoringActive'
        ));
    }

    public function monitoring()
    {
        $websites = Website::latest()->get();
        $monitoringIntervalSeconds = (int) SystemSetting::getValue(
            SystemSetting::MONITORING_INTERVAL_KEY,
            (string) SystemSetting::DEFAULT_MONITORING_INTERVAL
        );
        if (! in_array($monitoringIntervalSeconds, SystemSetting::MONITORING_INTERVAL_OPTIONS, true)) {
            $monitoringIntervalSeconds = SystemSetting::DEFAULT_MONITORING_INTERVAL;
        }

        $chartHistory = $websites->mapWithKeys(function (Website $website) {
            return [$website->id => $website->monitoringLogs()
                ->latest('checked_at')
                ->limit(16)
                ->get()
                ->reverse()
                ->map(function (MonitoringLog $log) {
                    return [
                        'score' => $log->status === 'Offline' || $log->response_time === null
                            ? 0
                            : max(0, min(100, (int) round(100 - ($log->response_time / 50)))),
                        'status' => $log->status,
                        'checked_at' => $log->checked_at->toIso8601String(),
                    ];
                })
                ->values()];
        });

        return view('dashboard.admin.monitoring.index', compact(
            'websites',
            'chartHistory',
            'monitoringIntervalSeconds'
        ));
    }

    public function index()
    {
        $websites = Website::latest()->get();
        $websiteListIntervalSeconds = (int) SystemSetting::getValue(
            SystemSetting::WEBSITE_LIST_INTERVAL_KEY,
            (string) SystemSetting::DEFAULT_WEBSITE_LIST_INTERVAL
        );
        if (! in_array($websiteListIntervalSeconds, SystemSetting::WEBSITE_LIST_INTERVAL_OPTIONS, true)) {
            $websiteListIntervalSeconds = SystemSetting::DEFAULT_WEBSITE_LIST_INTERVAL;
        }

        $chartHistory = $websites->mapWithKeys(function (Website $website) {
            return [$website->id => $website->monitoringLogs()
                ->latest('checked_at')
                ->limit(4)
                ->get()
                ->reverse()
                ->map(function (MonitoringLog $log) {
                    return [
                        'score' => $log->status === 'Offline' || $log->response_time === null
                            ? 0
                            : max(0, min(100, (int) round(100 - ($log->response_time / 50)))),
                        'status' => $log->status,
                        'checked_at' => $log->checked_at->toIso8601String(),
                    ];
                })
                ->values()];
        });

        return view('dashboard.admin.website.index', compact(
            'websites',
            'chartHistory',
            'websiteListIntervalSeconds'
        ));
    }

    public function create()
    {
        return view('dashboard.admin.website.create');
    }

    public function edit(Website $website)
    {
        return view('dashboard.admin.website.edit', compact('website'));
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'nama_website' => 'required|string|max:100',
        'instansi'     => ['required', Rule::in(config('opd'))],
        'url'          => 'required|url|max:255',
    ]);

        Website::create([
            'nama_website' => $request->nama_website,
            'instansi' => $request->instansi,
            'url' => $request->url,
            'status' => 'Belum Dicek',
            'monitoring_aktif' => true,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Website berhasil ditambahkan.');
    }

    public function update(Request $request, Website $website)
    {
        $validated = $request->validate([
            'nama_website' => 'required|string|max:100',
            'instansi' => 'required|string|max:100',
            'url' => 'required|url|max:255',
        ]);

        $website->update($validated);

        return redirect()
            ->route('website.index')
            ->with('success', 'Website berhasil diperbarui.');
    }

    public function destroy(Website $website)
    {
        $website->delete();

        return redirect()
            ->route('website.index')
            ->with('success', 'Website berhasil dihapus.');
    }
}
