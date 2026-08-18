<x-guest-layout>
    <div class="card-header">
        <h1>Buat Akun Baru</h1>
        <p>Daftarkan diri Anda untuk menggunakan sistem absensi</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Username --}}
        <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input
                    id="username"
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    placeholder="Masukkan username"
                    required
                    autofocus
                    autocomplete="username"
                    class="form-input"
                >
            </div>
            @error('username')
                <p class="error-text">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- No. HP --}}
        <div class="form-group">
            <label for="no_hp">Nomor HP</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </span>
                <input
                    id="no_hp"
                    type="tel"
                    name="no_hp"
                    value="{{ old('no_hp') }}"
                    placeholder="Contoh: 08123456789"
                    required
                    autocomplete="tel"
                    class="form-input"
                >
            </div>
            @error('no_hp')
                <p class="error-text">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Masukan Password"
                    required
                    autocomplete="new-password"
                    class="form-input has-action"
                >
                <button type="button" class="input-action" id="togglePassword" aria-label="Tampilkan password">
                    <svg id="eyeOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eyeClosed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="error-text">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Ulangi password"
                    required
                    autocomplete="new-password"
                    class="form-input"
                >
            </div>
            @error('password_confirmation')
                <p class="error-text">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login" id="registerBtn">
            <span class="btn-login-inner">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Daftar Sekarang
            </span>
        </button>

        <div style="text-align: center; margin-top: 16px;">
            <a href="{{ route('login') }}" class="forgot-link">
                Sudah punya akun? Masuk di sini
            </a>
        </div>
    </form>

    <script>
        (function () {
            var btn = document.getElementById('togglePassword');
            var pw  = document.getElementById('password');
            var e1  = document.getElementById('eyeOpen');
            var e2  = document.getElementById('eyeClosed');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var isText = pw.type === 'text';
                pw.type = isText ? 'password' : 'text';
                e1.style.display = isText ? 'block' : 'none';
                e2.style.display = isText ? 'none'  : 'block';
            });
        })();
    </script>
</x-guest-layout>
