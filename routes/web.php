<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\Master\AdminController;
use App\Http\Controllers\Admin\Master\DivisionController;
use App\Http\Controllers\Admin\Master\OfficeSettingController;
use App\Http\Controllers\Admin\Master\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\User\LeaveController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // User Attendance Dashboard
    Route::get('/', [AttendanceController::class, 'dashboard'])->name('dashboard');
    Route::post('/absen/masuk', [AttendanceController::class, 'clockIn'])->name('user.attendance.clock-in');
    Route::post('/absen/keluar', [AttendanceController::class, 'clockOut'])->name('user.attendance.clock-out');
    Route::get('/riwayat-absen', [AttendanceController::class, 'history'])->name('user.attendance.history');

    // Web Push Notifications
    Route::get('/push-vapid-key', [PushNotificationController::class, 'getPublicKey'])->name('push.key');
    Route::post('/push-subscribe', [PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push-unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::post('/push-test', [PushNotificationController::class, 'sendTest'])->name('push.test');

    // User Leaves (Pengajuan Izin)
    Route::get('/ajukan-izin', [LeaveController::class, 'create'])->name('user.leaves.create');
    Route::post('/ajukan-izin', [LeaveController::class, 'store'])->name('user.leaves.store');
    Route::get('/riwayat-izin', [LeaveController::class, 'index'])->name('user.leaves.history');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        // Divisi
        Route::get('/divisi', [DivisionController::class, 'index'])->name('divisi');
        Route::post('/divisi', [DivisionController::class, 'store'])->name('divisi.store');
        Route::put('/divisi/{divisi}', [DivisionController::class, 'update'])->name('divisi.update');
        Route::delete('/divisi/{divisi}', [DivisionController::class, 'destroy'])->name('divisi.destroy');

        // Data Admin
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');
        Route::put('/admin/{admin}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/admin/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');

        // Data User
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

        // Titik Absensi / Lokasi Kantor
        Route::get('/lokasi-kantor', [OfficeSettingController::class, 'index'])->name('lokasi-kantor');
        Route::put('/lokasi-kantor', [OfficeSettingController::class, 'update'])->name('lokasi-kantor.update');
    });

    Route::get('/absensi', [AdminAttendanceController::class, 'index'])->name('absensi');
    Route::delete('/absensi/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('absensi.destroy');

    Route::get('/karyawan', function () {
        return view('admin.master.data-user');
    })->name('karyawan');

    // Pengajuan Izin
    Route::get('/pengajuan-izin', [AdminLeaveController::class, 'index'])->name('pengajuan-izin');
    Route::post('/pengajuan-izin/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('pengajuan-izin.approve');
    Route::post('/pengajuan-izin/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('pengajuan-izin.reject');
    Route::delete('/pengajuan-izin/{leave}', [AdminLeaveController::class, 'destroy'])->name('pengajuan-izin.destroy');

    // Export & Unduh Laporan
    Route::get('/export', [AdminExportController::class, 'index'])->name('export');
    Route::prefix('export')->name('export.')->group(function () {
        // AJAX Previews
        Route::get('/absensi/preview', [AdminExportController::class, 'previewAbsensi'])->name('absensi.preview');
        Route::get('/izin/preview', [AdminExportController::class, 'previewIzin'])->name('izin.preview');
        Route::get('/rekap/preview', [AdminExportController::class, 'previewRekap'])->name('rekap.preview');

        // Unduh Presensi
        Route::get('/absensi/excel', [AdminExportController::class, 'exportAbsensiExcel'])->name('absensi.excel');
        Route::get('/absensi/pdf', [AdminExportController::class, 'exportAbsensiPdf'])->name('absensi.pdf');
        Route::get('/absensi/print', [AdminExportController::class, 'printAbsensi'])->name('absensi.print');

        // Unduh Izin & Cuti
        Route::get('/izin/excel', [AdminExportController::class, 'exportIzinExcel'])->name('izin.excel');
        Route::get('/izin/pdf', [AdminExportController::class, 'exportIzinPdf'])->name('izin.pdf');
        Route::get('/izin/print', [AdminExportController::class, 'printIzin'])->name('izin.print');

        // Unduh Rekap Bulanan
        Route::get('/rekap/excel', [AdminExportController::class, 'exportRekapExcel'])->name('rekap.excel');
        Route::get('/rekap/pdf', [AdminExportController::class, 'exportRekapPdf'])->name('rekap.pdf');
        Route::get('/rekap/print', [AdminExportController::class, 'printRekap'])->name('rekap.print');
    });
});

require __DIR__.'/auth.php';
