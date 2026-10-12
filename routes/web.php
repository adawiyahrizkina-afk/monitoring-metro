<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\SettingsController;
use App\Models\MonitoringLog;
use App\Services\MonitoringLogCleanup;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/', [AdminController::class, 'login'])
    ->name('login');

Route::post('/admin/login', [AdminController::class, 'authenticate'])
    ->name('login.process');


/*
|--------------------------------------------------------------------------
| ADMINh  
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/admin/dashboard', [WebsiteController::class, 'dashboard'])
        ->name('dashboard');


    // DAFTAR WEBSITE
    Route::get('/admin/website', [WebsiteController::class, 'index'])
        ->name('website.index');


    // TAMBAH WEBSITE
    Route::get('/admin/website/create', [WebsiteController::class, 'create'])
        ->name('website.create');

    // EDIT WEBSITE
    Route::get('/admin/website/{website}/edit', [WebsiteController::class, 'edit'])
        ->name('website.edit');

    Route::put('/admin/website/{website}', [WebsiteController::class, 'update'])
        ->name('website.update');


    // SIMPAN WEBSITE
    Route::post('/admin/website', [WebsiteController::class, 'store'])
        ->name('website.store');


    // CEK SATU WEBSITE
    Route::post('/admin/website/{website}/check', [MonitoringController::class, 'check'])
        ->name('website.check');


    // CEK SEMUA WEBSITE
    Route::post('/admin/websites/check-all', [MonitoringController::class, 'checkAll'])
        ->name('website.check.all');

    Route::post('/admin/monitoring/realtime', [MonitoringController::class, 'checkAllRealtime'])
        ->name('monitoring.realtime');


    // RIWAYAT
    Route::get('/admin/riwayat', function (Request $request) {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        app(MonitoringLogCleanup::class)->handle();

        $search = $validated['search'] ?? '';
        $websites = MonitoringLog::with('website')
            ->where('status', 'Offline')
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('website', function ($query) use ($search) {
                    $query->where('nama_website', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%");
                });
            })
            ->latest('checked_at')
            ->get()
            ->groupBy('website_id')
            ->map(function ($logs) {
                $latestLog = $logs->first();

                return [
                    'name' => $latestLog->website?->nama_website ?? 'Website dihapus',
                    'url' => $latestLog->website?->url,
                    'incidents' => $logs,
                ];
            })
            ->values();

        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $websitePage = new LengthAwarePaginator(
            $websites->slice(($page - 1) * $perPage, $perPage)->values(),
            $websites->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('dashboard.admin.riwayat.index', [
            'websites' => $websitePage,
            'search' => $search,
        ]);
    })->name('riwayat.index');


    // MONITORING
    Route::get('/admin/monitoring', [WebsiteController::class, 'monitoring'])
        ->name('monitoring.index');


    // PENGATURAN
    Route::get('/admin/pengaturan', [SettingsController::class, 'index'])
        ->name('pengaturan.index');

    Route::post('/admin/pengaturan', [SettingsController::class, 'update'])
        ->name('pengaturan.update');


    // HAPUS WEBSITE
    Route::delete('/admin/website/{website}', [WebsiteController::class, 'destroy'])
        ->name('website.destroy');

    // HAPUS SEMUA LOG MONITORING
    Route::delete('/admin/riwayat', [MonitoringController::class, 'destroyAllLogs'])
        ->name('riwayat.destroy');


    // LOGOUT
    Route::post('/admin/logout', [AdminController::class, 'logout'])
        ->name('logout');
});
