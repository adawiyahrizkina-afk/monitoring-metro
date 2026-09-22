<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\MonitoringController;


/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/login',
    [AdminController::class, 'login']
)->name('login');


Route::post(
    '/admin/login',
    [AdminController::class, 'authenticate']
)->name('login.process');


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/admin/dashboard',
        [WebsiteController::class, 'dashboard']
    )->name('dashboard');


    Route::post(
        '/admin/logout',
        [AdminController::class, 'logout']
    )->name('logout');


    Route::post(
        '/admin/website/{website}/check',
        [MonitoringController::class, 'check']
    )->name('website.check');

    Route::post(
          '/admin/websites/check-all',
         [MonitoringController::class, 'checkAll']
    )->name('website.check.all');

});