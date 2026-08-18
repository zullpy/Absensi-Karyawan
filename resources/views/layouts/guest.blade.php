<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="HADIRin – Sistem absensi karyawan modern dari genggaman tangan Anda.">

        <title>{{ $title ?? config('app.name', 'HADIRin - Absensi dari Genggaman') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ===== DESIGN TOKENS – LIGHT ===== */
            :root {
                --primary:          #2563eb;
                --primary-hover:    #1d4ed8;
                --primary-muted:    rgba(37,99,235,0.12);
                --primary-border:   rgba(37,99,235,0.3);

                --bg-page:          #f1f4f9;
                --bg-card:          #ffffff;
                --bg-input:         #f8fafc;
                --border-card:      #e2e8f0;
                --border-input:     #cbd5e1;
                --border-focus:     #2563eb;

                --text-primary:     #0f172a;
                --text-secondary:   #475569;
                --text-muted:       #94a3b8;
                --text-placeholder: #94a3b8;

                --shadow-card:      0 1px 3px rgba(15,23,42,0.08), 0 8px 32px rgba(15,23,42,0.06);
                --shadow-btn:       0 2px 8px rgba(37,99,235,0.25);
                --ring-focus:       0 0 0 3px rgba(37,99,235,0.18);

                --radius-card:      20px;
                --radius-input:     12px;
                --radius-btn:       12px;
                --transition:       all 0.2s ease;
            }

            /* ===== DESIGN TOKENS – DARK ===== */
            html.dark {
                --primary:          #3b82f6;
                --primary-hover:    #2563eb;
                --primary-muted:    rgba(59,130,246,0.1);
                --primary-border:   rgba(59,130,246,0.2);

                --bg-page:          #0f172a;
                --bg-card:          #1e293b;
                --bg-input:         #162032;
                --border-card:      #2d3f55;
                --border-input:     #2d3f55;
                --border-focus:     #3b82f6;

                --text-primary:     #e2e8f0;
                --text-secondary:   #94a3b8;
                --text-muted:       #64748b;
                --text-placeholder: #475569;

                --shadow-card:      0 1px 3px rgba(0,0,0,0.3), 0 8px 32px rgba(0,0,0,0.2);
                --shadow-btn:       0 2px 8px rgba(59,130,246,0.2);
                --ring-focus:       0 0 0 3px rgba(59,130,246,0.15);
            }

            /* ===== BASE ===== */
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
                background: var(--bg-page);
                min-height: 100dvh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                transition: background 0.3s ease;
                -webkit-font-smoothing: antialiased;
            }

            /* ===== THEME TOGGLE ===== */
            .theme-toggle-wrap {
                position: fixed;
                top: 16px;
                right: 16px;
                z-index: 100;
            }
            .theme-toggle {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                border: 1px solid var(--border-card);
                background: var(--bg-card);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: var(--transition);
                color: var(--text-secondary);
                box-shadow: var(--shadow-card);
            }
            .theme-toggle:hover {
                border-color: var(--primary);
                color: var(--primary);
            }
            .theme-toggle svg { width: 18px; height: 18px; }
            .icon-sun  { display: none; }
            .icon-moon { display: block; }
            html.dark .icon-sun  { display: block; }
            html.dark .icon-moon { display: none; }

            /* ===== WRAPPER ===== */
            .guest-wrapper {
                width: 100%;
                min-height: 100dvh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 24px 16px 40px;
            }

            /* ===== LOGO SECTION ===== */
            .logo-section {
                text-align: center;
                margin-bottom: 24px;
            }
            .logo-svg {
                height: 130px;
                width: auto;
                display: block;
                margin: 0 auto;
                /* Logo PNG is white/mint — invert to dark navy for light mode */
                filter: invert(1) invert(18%) sepia(60%) saturate(500%) hue-rotate(195deg) brightness(75%) contrast(120%);
            }
            html.dark .logo-svg {
                /* Dark mode: show original white/mint color from the PNG */
                filter: none;
                opacity: 0.9;
            }
            .logo-app-name {
                font-size: 20px;
                font-weight: 700;
                color: var(--text-primary);
                letter-spacing: -0.3px;
            }
            .logo-tagline {
                font-size: 13px;
                color: var(--text-muted);
                margin-top: 3px;
                font-weight: 400;
            }

            /* ===== CARD ===== */
            .login-card {
                width: 100%;
                max-width: 400px;
                background: var(--bg-card);
                border: 1px solid var(--border-card);
                border-radius: var(--radius-card);
                box-shadow: var(--shadow-card);
                padding: 32px 28px;
                transition: background 0.3s ease, border-color 0.3s ease;
            }

            /* ===== CARD HEADER ===== */
            .card-header {
                margin-bottom: 24px;
            }
            .card-header h1 {
                font-size: 18px;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 4px;
                letter-spacing: -0.2px;
            }
            .card-header p {
                font-size: 13px;
                color: var(--text-secondary);
                line-height: 1.5;
            }

            /* ===== FORM ===== */
            .form-group { margin-bottom: 16px; }
            .form-group label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: var(--text-primary);
                margin-bottom: 6px;
            }
            .input-wrap { position: relative; }
            .input-icon {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--text-muted);
                pointer-events: none;
                transition: color 0.2s ease;
            }
            .input-icon svg { width: 16px; height: 16px; display: block; }
            .input-wrap:focus-within .input-icon { color: var(--primary); }

            .form-input {
                width: 100%;
                padding: 10px 12px 10px 38px;
                background: var(--bg-input);
                border: 1px solid var(--border-input);
                border-radius: var(--radius-input);
                color: var(--text-primary);
                font-size: 14px;
                font-family: 'Inter', sans-serif;
                outline: none;
                transition: var(--transition);
            }
            .form-input::placeholder { color: var(--text-placeholder); font-size: 13px; }
            .form-input:focus {
                border-color: var(--border-focus);
                box-shadow: var(--ring-focus);
                background: var(--bg-card);
            }
            .form-input.has-action { padding-right: 38px; }

            .input-action {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                color: var(--text-muted);
                padding: 4px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                transition: color 0.2s ease;
            }
            .input-action:hover { color: var(--primary); }
            .input-action svg { width: 16px; height: 16px; display: block; }

            /* ===== FORGOT ===== */
            .forgot-link {
                display: inline-block;
                margin-top: 6px;
                font-size: 12px;
                color: var(--text-muted);
                text-decoration: none;
                transition: color 0.2s ease;
            }
            .forgot-link:hover { color: var(--primary); }

            /* ===== REMEMBER ===== */
            .remember-row {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 20px;
            }
            .custom-checkbox {
                width: 16px;
                height: 16px;
                accent-color: var(--primary);
                cursor: pointer;
                flex-shrink: 0;
            }
            .remember-label {
                font-size: 13px;
                color: var(--text-secondary);
                cursor: pointer;
                user-select: none;
            }

            /* ===== BUTTON ===== */
            .btn-login {
                width: 100%;
                padding: 11px 20px;
                background: var(--primary);
                color: #fff;
                font-size: 14px;
                font-weight: 600;
                font-family: 'Inter', sans-serif;
                border: none;
                border-radius: var(--radius-btn);
                cursor: pointer;
                box-shadow: var(--shadow-btn);
                transition: var(--transition);
            }
            .btn-login:hover {
                background: var(--primary-hover);
                box-shadow: 0 4px 14px rgba(37,99,235,0.3);
            }
            .btn-login:active { transform: translateY(1px); }
            .btn-login-inner {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
            }
            .btn-login-inner svg { width: 16px; height: 16px; }

            /* ===== SESSION ALERT ===== */
            .session-alert {
                background: var(--primary-muted);
                border: 1px solid var(--primary-border);
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 13px;
                color: var(--primary);
                margin-bottom: 16px;
            }

            /* ===== ERROR ===== */
            .error-text {
                color: #ef4444;
                font-size: 12px;
                margin-top: 4px;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .error-text svg { width: 12px; height: 12px; flex-shrink: 0; }

            /* ===== FOOTER ===== */
            .guest-footer {
                margin-top: 20px;
                text-align: center;
                font-size: 12px;
                color: var(--text-muted);
            }

            /* ===== RESPONSIVE ===== */
            @media (max-width: 480px) {
                .login-card { padding: 24px 20px; border-radius: 16px; }
            }
        </style>
    </head>
    <body>

        <!-- Theme toggle -->
        <div class="theme-toggle-wrap">
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
                <svg class="icon-sun" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                <svg class="icon-moon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>
        </div>

        <div class="guest-wrapper">
            <!-- Logo -->
            <div class="logo-section">
                <img src="{{ asset('images/logo/logo-hadirin.png') }}" alt="HADIRin Logo" class="logo-svg">
            </div>

            <!-- Card -->
            <div class="login-card">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <footer class="guest-footer">
                &copy; {{ date('Y') }} HADIRin &mdash; Semua hak dilindungi
            </footer>
        </div>

        <script>
            (function () {
                var saved = localStorage.getItem('hadirin-theme');
                if (saved === 'dark') {
                    document.documentElement.classList.add('dark');
                }
                document.addEventListener('DOMContentLoaded', function () {
                    var btn = document.getElementById('themeToggle');
                    if (!btn) return;
                    btn.addEventListener('click', function () {
                        var isDark = document.documentElement.classList.toggle('dark');
                        localStorage.setItem('hadirin-theme', isDark ? 'dark' : 'light');
                    });
                });
            })();
        </script>
    </body>
</html>
