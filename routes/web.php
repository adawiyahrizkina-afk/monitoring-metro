<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\SettingsController;
use App\Services\MonitoringLogCleanup;


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
| ADMIN
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


    // SIMPAN WEBSITE
    Route::post('/admin/website', [WebsiteController::class, 'store'])
        ->name('website.store');


    // CEK SATU WEBSITE
    Route::post('/admin/website/{website}/check', [MonitoringController::class, 'check'])
        ->name('website.check');


    // CEK SEMUA WEBSITE
    Route::post('/admin/websites/check-all', [MonitoringController::class, 'checkAll'])
        ->name('website.check.all');


    // RIWAYAT
    Route::get('/admin/riwayat', function () {
        app(MonitoringLogCleanup::class)->handle();
        $logs = \App\Models\MonitoringLog::with('website')
            ->latest('checked_at')
            ->get();
        return view('dashboard.admin.riwayat.index', compact('logs'));
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


    // LOGOUT
    Route::post('/admin/logout', [AdminController::class, 'logout'])
        ->name('logout');
});
