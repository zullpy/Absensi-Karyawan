<x-guest-layout>
    <div class="card-header">
        <h1>Lupa Kata Sandi?</h1>
        <p>Tidak masalah. Masukkan nomor HP Anda dan kami akan mengirimkan tautan untuk mereset kata sandi Anda.</p>
    </div>

    @if (session('status'))
        <div class="session-alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="no_hp">Nomor HP</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </span>
                <input
                    id="no_hp"
                    class="form-input @error('no_hp') border-red-500 @enderror"
                    type="tel"
                    name="no_hp"
                    value="{{ old('no_hp') }}"
                    placeholder="Contoh: 08123456789"
                    required
                    autofocus
                    autocomplete="tel"
                >
            </div>
            @error('no_hp')
                <div class="error-text">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn-login" id="btn-kirim-reset">
            <span class="btn-login-inner">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Kirim Tautan Reset
            </span>
        </button>

        <div style="text-align: center; margin-top: 16px;">
            <a href="{{ route('login') }}" class="forgot-link">
                ← Kembali ke halaman login
            </a>
        </div>
    </form>

</x-guest-layout>
