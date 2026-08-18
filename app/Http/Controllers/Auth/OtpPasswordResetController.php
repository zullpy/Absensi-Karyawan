<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class OtpPasswordResetController extends Controller
{
    public function __construct(private FonnteService $fonnte) {}

    // ─────────────────────────────────────────────
    // STEP 1 — Tampilkan form input no. HP
    // ─────────────────────────────────────────────
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    // ─────────────────────────────────────────────
    // STEP 2 — Kirim OTP ke WhatsApp
    // ─────────────────────────────────────────────
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'no_hp' => ['required', 'string'],
        ], [
            'no_hp.required' => 'Nomor HP wajib diisi.',
        ]);

        // Rate limit: maks 3 kali per menit per IP
        $key = 'otp-send:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'no_hp' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $user = User::where('no_hp', $request->no_hp)->first();

        if (! $user) {
            RateLimiter::hit($key, 60);
            return back()
                ->withInput()
                ->withErrors(['no_hp' => 'Nomor HP tidak ditemukan.']);
        }

        // Hapus OTP lama untuk nomor ini
        DB::table('password_resets_otp')->where('no_hp', $request->no_hp)->delete();

        // Generate OTP 6 digit
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan ke database
        DB::table('password_resets_otp')->insert([
            'no_hp'      => $request->no_hp,
            'otp'        => $otp,
            'expired_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        // Kirim via WhatsApp
        $sent = $this->fonnte->sendOtp($request->no_hp, $otp);

        RateLimiter::hit($key, 60);

        if (! $sent) {
            return back()->withInput()->withErrors([
                'no_hp' => 'Gagal mengirim OTP. Pastikan nomor HP Anda aktif di WhatsApp.',
            ]);
        }

        // Simpan no_hp di session untuk langkah berikutnya
        $request->session()->put('otp_no_hp', $request->no_hp);

        return redirect()->route('password.otp.verify')
            ->with('status', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }

    // ─────────────────────────────────────────────
    // STEP 3 — Tampilkan form verifikasi OTP
    // ─────────────────────────────────────────────
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('otp_no_hp')) {
            return redirect()->route('password.request');
        }

        return view('auth.otp-verify');
    }

    // ─────────────────────────────────────────────
    // STEP 4 — Verifikasi OTP
    // ─────────────────────────────────────────────
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus 6 digit.',
        ]);

        $no_hp = $request->session()->get('otp_no_hp');
        if (! $no_hp) {
            return redirect()->route('password.request');
        }

        $record = DB::table('password_resets_otp')
            ->where('no_hp', $no_hp)
            ->latest('created_at')
            ->first();

        if (! $record || $record->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
        }

        if (now()->isAfter($record->expired_at)) {
            DB::table('password_resets_otp')->where('no_hp', $no_hp)->delete();
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta OTP baru.']);
        }

        // Tandai OTP sudah diverifikasi
        $request->session()->put('otp_verified', true);

        return redirect()->route('password.otp.reset');
    }

    // ─────────────────────────────────────────────
    // STEP 5 — Tampilkan form password baru
    // ─────────────────────────────────────────────
    public function showResetForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.otp-reset-password');
    }

    // ─────────────────────────────────────────────
    // STEP 6 — Simpan password baru
    // ─────────────────────────────────────────────
    public function resetPassword(Request $request): RedirectResponse
    {
        if (! $request->session()->get('otp_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        $no_hp = $request->session()->get('otp_no_hp');
        $user  = User::where('no_hp', $no_hp)->firstOrFail();

        $user->update(['password' => Hash::make($request->password)]);

        // Bersihkan OTP dan session
        DB::table('password_resets_otp')->where('no_hp', $no_hp)->delete();
        $request->session()->forget(['otp_no_hp', 'otp_verified']);

        return redirect()->route('login')
            ->with('status', 'Password berhasil direset. Silakan masuk dengan password baru Anda.');
    }
}
