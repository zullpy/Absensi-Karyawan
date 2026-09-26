<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal otomatis kirim notifikasi Web Push 15 menit sebelum batas absen (08:15 WIB)
Schedule::command('attendance:send-reminders')
    ->dailyAt('08:15')
    ->timezone('Asia/Jakarta')
    ->description('Kirim pengingat absensi 15 menit sebelum batas waktu masuk 08:30 WIB');
