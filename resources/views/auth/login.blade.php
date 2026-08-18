<x-guest-layout>
    @if (session('status'))
        <div class="session-alert" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <!-- Card Header -->
    <div class="card-header">
        <h1>Masuk ke akun Anda</h1>
        <p>Silahkan masuk menggunakan username atau nomor HP Anda</p>
    </div>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Username -->
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
                    placeholder="Masukkan username atau no. HP"
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

        <!-- Password -->
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
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
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

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="remember-row">
            <input type="checkbox" id="remember_me" name="remember" class="custom-checkbox">
            <label for="remember_me" class="remember-label">Ingat saya di perangkat ini</label>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-login" id="loginBtn">
            <span class="btn-login-inner">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Masuk
            </span>
        </button>
    </form>

    <script>
        // Password toggle
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

        // Loading state
        (function () {
            var form = document.querySelector('form');
            var btn  = document.getElementById('loginBtn');
            if (!form || !btn) return;
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<span class="btn-login-inner" style="opacity:0.7">Memproses...</span>';
            });
        })();
    </script>
</x-guest-layout>
