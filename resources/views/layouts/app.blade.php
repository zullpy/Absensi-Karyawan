<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Aplikasi Absensi') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48" type="image/x-icon">
        <link rel="icon" href="{{ asset('images/logo/logo-hadirin.png') }}" sizes="any" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('images/logo/logo-hadirin.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Theme Initialization -->
        <script>
            (function () {
                var saved = localStorage.getItem('hadirin-theme');
                if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
        <style>
            /* Custom SweetAlert2 Theme for Light & Dark Mode */
            .swal2-container {
                backdrop-filter: blur(3px);
                -webkit-backdrop-filter: blur(3px);
            }
            .swal2-popup {
                border-radius: 1.25rem !important;
                font-family: inherit !important;
                padding: 1.75rem 1.5rem !important;
            }
            .swal2-title {
                font-size: 1.15rem !important;
                font-weight: 700 !important;
                line-height: 1.4 !important;
            }
            .swal2-html-container {
                font-size: 0.8125rem !important;
                line-height: 1.5 !important;
                margin-top: 0.5rem !important;
            }
            .swal2-actions {
                gap: 0.5rem !important;
                margin-top: 1.25rem !important;
            }
            .swal2-confirm, .swal2-cancel {
                border-radius: 0.625rem !important;
                font-size: 0.8125rem !important;
                font-weight: 600 !important;
                padding: 0.6rem 1.25rem !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            }
            
            /* Dark Mode Overrides */
            html.dark .swal2-popup {
                background: #1e293b !important;
                color: #f8fafc !important;
                border: 1px solid #334155 !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;
            }
            html.dark .swal2-title {
                color: #f8fafc !important;
            }
            html.dark .swal2-html-container {
                color: #94a3b8 !important;
            }
            html.dark .swal2-close {
                color: #94a3b8 !important;
            }
            html.dark .swal2-close:hover {
                color: #f8fafc !important;
            }
            html.dark .swal2-cancel {
                background-color: #334155 !important;
                color: #e2e8f0 !important;
                border: 1px solid #475569 !important;
            }
            html.dark .swal2-cancel:hover {
                background-color: #475569 !important;
            }
            html.dark .swal2-input, html.dark .swal2-textarea {
                background-color: #0f172a !important;
                color: #f8fafc !important;
                border: 1px solid #334155 !important;
            }

            /* SweetAlert2 Icon Enhancements in Dark Mode */
            html.dark .swal2-icon.swal2-warning {
                border-color: #f59e0b !important;
                color: #f59e0b !important;
            }
            html.dark .swal2-icon.swal2-question {
                border-color: #38bdf8 !important;
                color: #38bdf8 !important;
            }
            html.dark .swal2-icon.swal2-success {
                border-color: #10b981 !important;
                color: #10b981 !important;
            }
            html.dark .swal2-icon.swal2-success [class^='swal2-success-line'] {
                background-color: #10b981 !important;
            }
            html.dark .swal2-icon.swal2-success .swal2-success-ring {
                border-color: rgba(16, 185, 129, 0.3) !important;
            }
            html.dark .swal2-icon.swal2-error {
                border-color: #ef4444 !important;
                color: #ef4444 !important;
            }
            html.dark .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
                background-color: #ef4444 !important;
            }
        </style>
        <script>
            // Automatically adapt SweetAlert2 parameters to active theme
            (function() {
                if (window.Swal) {
                    const originalFire = Swal.fire.bind(Swal);
                    Swal.fire = function(...args) {
                        const isDark = document.documentElement.classList.contains('dark');
                        if (args.length === 1 && typeof args[0] === 'object' && args[0] !== null) {
                            if (!args[0].background) {
                                args[0].background = isDark ? '#1e293b' : '#ffffff';
                            }
                            if (!args[0].color) {
                                args[0].color = isDark ? '#f8fafc' : '#1e293b';
                            }
                        }
                        return originalFire(...args);
                    };
                }
            })();
        </script>

        <!-- Cropper.js for Profile Photo Editor -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
        <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-16 sm:pb-0">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
