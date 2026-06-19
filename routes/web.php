<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\KonfirmasiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\KonfirmasiController as AdminKonfirmasiController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\ProsesScanController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/konfirmasi', [KonfirmasiController::class, 'showForm'])->name('konfirmasi');
Route::post('/konfirmasi', [KonfirmasiController::class, 'store']);

Route::get('/file/bukti-undangan/{filename}', [FileController::class, 'serveBuktiUndangan'])->name('file.bukti-undangan');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/admin/events', [EventController::class, 'index'])->name('admin.events.index');
    Route::get('/admin/events/create', [EventController::class, 'create'])->name('admin.events.create');
    Route::post('/admin/events', [EventController::class, 'store'])->name('admin.events.store');
    Route::get('/admin/events/{id}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
    Route::post('/admin/events/{id}', [EventController::class, 'update'])->name('admin.events.update');
    Route::post('/admin/events/{id}/delete', [EventController::class, 'destroy'])->name('admin.events.destroy');
    
    Route::post('/admin/events/{id}/sessions', [EventController::class, 'addSession'])->name('admin.events.sessions.store');
    Route::post('/admin/events/sessions/{id}/update', [EventController::class, 'updateSession'])->name('admin.events.sessions.update');
    Route::post('/admin/events/sessions/{id}/delete', [EventController::class, 'deleteSession'])->name('admin.events.sessions.delete');
    
    Route::post('/admin/events/{id}/invite', [EventController::class, 'inviteUsers'])->name('admin.events.invite');
    Route::post('/admin/events/{id}/auto-confirm', [EventController::class, 'autoConfirm'])->name('admin.events.auto-confirm');
    
    Route::get('/admin/konfirmasi', [AdminKonfirmasiController::class, 'index'])->name('admin.konfirmasi.index');
    Route::get('/admin/konfirmasi/{eventId}', [AdminKonfirmasiController::class, 'index'])->name('admin.konfirmasi.event');
    Route::post('/admin/konfirmasi/{id}/acc', [AdminKonfirmasiController::class, 'acc'])->name('admin.konfirmasi.acc');
    Route::get('/admin/konfirmasi/{id}/wa', [AdminKonfirmasiController::class, 'wa'])->name('admin.konfirmasi.wa');
    Route::post('/admin/konfirmasi/{id}/hapus', [AdminKonfirmasiController::class, 'hapus'])->name('admin.konfirmasi.hapus');
    Route::post('/admin/konfirmasi/{eventId}/acc-all', [AdminKonfirmasiController::class, 'accAll'])->name('admin.konfirmasi.acc-all');
    Route::get('/admin/konfirmasi/{eventId}/export', [AdminKonfirmasiController::class, 'exportCsv'])->name('admin.konfirmasi.export');

    Route::get('/admin/absensi', [AbsensiController::class, 'index'])->name('admin.absensi.index');
    Route::get('/admin/absensi/{eventId}', [AbsensiController::class, 'index'])->name('admin.absensi.event');
    Route::get('/admin/absensi/{eventId}/export-csv', [AbsensiController::class, 'exportCsv'])->name('admin.absensi.export-csv');

Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::post('/admin/users/bulk-delete', [UserController::class, 'destroyBulk'])->name('admin.users.bulk-delete');
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{id}/delete', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/import', [UserController::class, 'importCsv'])->name('admin.users.import');
    Route::get('/admin/users/export', [UserController::class, 'exportCsv'])->name('admin.users.export');
    Route::get('/admin/users/export-excel', [UserController::class, 'exportExcel'])->name('admin.users.export-excel');
    
    Route::get('/admin/history', [HistoryController::class, 'index'])->name('admin.history');
    
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/dashboard/absen', [UserDashboardController::class, 'absen'])->name('user.absen');
    Route::post('/dashboard/materi', [UserDashboardController::class, 'konfirmasiMateri'])->name('user.materi');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::post('/profile', [UserDashboardController::class, 'updateProfile'])->name('user.profile.update');
    
    Route::get('/certificate', [CertificateController::class, 'index'])->name('certificate');
    
    Route::get('/monitor/{eventId}', [MonitorController::class, 'index'])->name('monitor.index');
    Route::get('/monitor/{eventId}/qr', [MonitorController::class, 'generateQr'])->name('monitor.qr');
    Route::get('/monitor/{eventId}/session', [MonitorController::class, 'getSessionInfo'])->name('monitor.session');
    
Route::get('/download-qr/{eventId}', [QrController::class, 'download'])->name('download.qr');
    
Route::get('/proses-scan/{eventId}', [ProsesScanController::class, 'proses'])->name('proses.scan');
});

Route::prefix('maintenance')->group(function () {
    Route::get('/clear-config', [MaintenanceController::class, 'clearConfig']);
    Route::get('/clear-view', [MaintenanceController::class, 'clearView']);
    Route::get('/optimize', [MaintenanceController::class, 'optimize']);
    Route::get('/clear-all', [MaintenanceController::class, 'clearAll']);
    Route::get('/migrate', [MaintenanceController::class, 'migrate']);
    Route::get('/migrate-rollback', [MaintenanceController::class, 'migrateRollback']);
    Route::get('/migrate-status', [MaintenanceController::class, 'migrateStatus']);
    Route::get('/db-status', [MaintenanceController::class, 'dbStatus']);
    Route::get('/storage-link', [MaintenanceController::class, 'storageLink']);
});

Route::get('/debug-session-check', function() {
    date_default_timezone_set('Asia/Jakarta');
    $userId = \Illuminate\Support\Facades\Session::get('user_id');
    $today = date('Y-m-d');
    $now = date('H:i:s');
    $myEvents = \App\Models\EventRegistration::where('user_id', $userId)
        ->with('event.sessions')
        ->whereHas('event', function($q) use ($today) {
            $q->where('tanggal', '>=', $today);
        })
        ->get();
    $output = "User ID: $userId<br>Today: $today<br>Now: $now<br>Timezone: " . date_default_timezone_get() . "<br><br>";
    $output .= "myEvents count: " . $myEvents->count() . "<br><br>";
    foreach ($myEvents as $reg) {
        $output .= "Reg ID: {$reg->id}, Event: {$reg->event->nama_event}, Tanggal: {$reg->event->tanggal}<br>";
        foreach ($reg->event->sessions as $s) {
            $isActive = ($now >= $s->jam_mulai && $now <= $s->jam_selesai) ? 'YES ACTIVE' : 'no';
            $output .= "  - {$s->nama_sesi}: {$s->jam_mulai} - {$s->jam_selesai} [$isActive]<br>";
        }
        $output .= "<br>";
    }
    return $output;
});
