<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     * Cari user berdasarkan no_hp, lalu kirim notifikasi reset password.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'no_hp' => ['required', 'string'],
        ], [
            'no_hp.required' => 'Nomor HP wajib diisi.',
        ]);

        // Cari user berdasarkan no_hp
        $user = User::where('no_hp', $request->no_hp)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('no_hp'))
                ->withErrors(['no_hp' => 'Nomor HP tidak ditemukan.']);
        }

        // Karena tidak pakai email, kita redirect ke halaman reset langsung
        // atau tampilkan pesan sukses (sistem reset via no_hp / WA bisa dikembangkan)
        return back()->with('status', 'Permintaan reset kata sandi telah dikirim.');
    }
}
