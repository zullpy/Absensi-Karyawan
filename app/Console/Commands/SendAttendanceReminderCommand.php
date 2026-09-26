<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAttendanceReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-reminders 
                            {--force : Force send reminders regardless of current time}
                            {--user= : Target specific user ID for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi Web Push ke karyawan yang belum absen 15 menit sebelum batas waktu masuk';

    /**
     * Execute the console command.
     */
    public function handle(WebPushService $webPushService): int
    {
        $this->info('Memeriksa jadwal pengingat absensi...');

        $now = Carbon::now(); // In Asia/Jakarta (WIB)
        $today = $now->toDateString();

        $deadlineStr = config('webpush.reminder.clock_in_deadline', '08:30:00');
        $minutesBefore = (int) config('webpush.reminder.minutes_before', 15);

        $deadlineTime = Carbon::createFromTimeString($deadlineStr);
        $targetReminderTime = $deadlineTime->copy()->subMinutes($minutesBefore);

        $force = $this->option('force');
        $targetUserId = $this->option('user');

        // Check if within reminder window (target time +/- 2 minutes) unless force is used
        if (!$force && !$targetUserId) {
            $diffInMinutes = abs($now->diffInMinutes($targetReminderTime, false));
            if ($diffInMinutes > 2) {
                $this->warn("Saat ini pukul {$now->format('H:i')} WIB. Waktu pengingat adalah pukul {$targetReminderTime->format('H:i')} WIB (15 menit sebelum batas {$deadlineStr} WIB).");
                $this->line("Gunakan flag --force jika ingin mengirim secara manual sekarang.");
                return Command::SUCCESS;
            }
        }

        // Query employees who have not clocked in today
        $query = User::where('role', 'user')
            ->whereHas('pushSubscriptions')
            ->whereDoesntHave('attendances', function ($q) use ($today) {
                $q->where('date', $today)->whereNotNull('time_in');
            });

        if ($targetUserId) {
            $query->where('id', $targetUserId);
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            $this->info("Tidak ada karyawan yang perlu diingatkan (semua sudah absen atau belum ada perangkat terdaftar).");
            return Command::SUCCESS;
        }

        $this->info("Mengirim pengingat ke {$employees->count()} karyawan...");
        $sentCount = 0;

        foreach ($employees as $employee) {
            $results = $webPushService->sendAttendanceReminder(
                $employee,
                $minutesBefore,
                $deadlineTime->format('H:i') . ' WIB'
            );

            if (!empty(array_filter($results))) {
                $sentCount++;
                $this->line("  ✓ Terkirim ke: {$employee->username}");
            } else {
                $this->warn("  ✗ Gagal mengirim ke: {$employee->username}");
            }
        }

        $this->info("Selesai! Pengingat berhasil terkirim ke {$sentCount} dari {$employees->count()} karyawan.");
        return Command::SUCCESS;
    }
}
