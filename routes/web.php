<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/divisi', function () {
            return view('admin.master.divisi');
        })->name('divisi');

        Route::get('/admin', function () {
            return view('admin.master.data-admin');
        })->name('admin');

        Route::get('/user', function () {
            return view('admin.master.data-user');
        })->name('user');
    });

    Route::get('/absensi', function () {
        return view('admin.absensi');
    })->name('absensi');

    Route::get('/karyawan', function () {
        return view('admin.master.data-user');
    })->name('karyawan');

    Route::get('/pengajuan-izin', function () {
        return view('admin.pengajuan-izin');
    })->name('pengajuan-izin');

    Route::get('/export', function () {
        return view('admin.export');
    })->name('export');
});

require __DIR__.'/auth.php';
