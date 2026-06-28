<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\LogHistorisController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MonitoringApiController;

// ── Route Autentikasi (tanpa middleware) ──
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Route yang Membutuhkan Login ──
Route::middleware(['auth'])->group(function () {

     // Dashboard
     Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

     // Manajemen Perangkat
     Route::resource('devices', DeviceController::class);

     // Perbaikan di baris ini: name disesuaikan menjadi api.devices.ping sesuai kebutuhan view
     Route::post('devices/{device}/ping', [MonitoringApiController::class, 'pingOne'])
          ->name('api.devices.ping');

     // Log Historis
     Route::get('/logs', [LogHistorisController::class, 'index'])->name('logs.index');

     // Laporan PDF
     Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
     Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');

     // API Monitoring Real-Time
     Route::post('/api/monitor/run', [MonitoringApiController::class, 'runCheck'])
          ->name('api.monitor.run');
     Route::get('/api/monitor/status', [MonitoringApiController::class, 'getStatus'])
          ->name('api.monitor.status');
     Route::get('/api/monitor/notifications', [MonitoringApiController::class, 'getRecentNotifications'])
          ->name('api.monitor.notifications');

     Route::get('/admin/profil', function () {
          return view('admin.profil');
     })->name('admin.profil');
});