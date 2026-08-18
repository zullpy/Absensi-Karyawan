<x-guest-layout>
    <div class="card-header">
        <h1>Buat Password Baru</h1>
        <p>Masukkan password baru Anda. Minimal 8 karakter.</p>
    </div>

    <form method="POST" action="{{ route('password.otp.reset.post') }}">
        @csrf

        {{-- Password Baru --}}
        <div class="form-group">
            <label for="password">Password Baru</label>
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
                    placeholder="Minimal 8 karakter"
                    required
                    autofocus
                    autocomplete="new-password"
                    class="form-input has-action"
                >
                <button type="button" class="input-action" id="togglePw" aria-label="Tampilkan password">
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
                    placeholder="Ulangi password baru"
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

        <button type="submit" class="btn-login" id="btnReset">
            <span class="btn-login-inner">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Simpan Password Baru
            </span>
        </button>
    </form>

    <script>
        (function () {
            var btn = document.getElementById('togglePw');
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
