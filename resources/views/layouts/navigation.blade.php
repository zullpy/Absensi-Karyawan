<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu (Desktop) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <img class="h-8 w-8 rounded-full object-cover ring-2 ring-blue-500/20 dark:ring-blue-400/20" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->username }}">
                            <div class="font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->username }}</div>

                            <div class="ms-0.5 text-gray-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button type="button" id="bnav-logout-desktop"
                            class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                            {{ __('Logout') }}
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile: Tampilkan foto dan nama user di kanan -->
            <div class="flex items-center sm:hidden">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 py-1 px-2 rounded-lg active:bg-gray-100 dark:active:bg-gray-800 transition">
                    <img class="h-7 w-7 rounded-full object-cover ring-1 ring-blue-500/30 dark:ring-blue-400/30" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->username }}">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ Auth::user()->username ?? '' }}
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hidden form untuk logout -->
<form method="POST" action="{{ route('logout') }}" id="logoutFormDesktop" class="hidden">
    @csrf
</form>

<!-- ===== BOTTOM NAVIGATION BAR (Mobile Only) ===== -->
<div class="bottom-nav sm:hidden" id="bottomNav">
    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}"
       class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       id="bnav-dashboard">
        <span class="bottom-nav-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
        </span>
        <span class="bottom-nav-label">Beranda</span>
    </a>

    {{-- Profil --}}
    <a href="{{ route('profile.edit') }}"
       class="bottom-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
       id="bnav-profile">
        <span class="bottom-nav-icon">
            <img class="w-5 h-5 rounded-full object-cover ring-1 {{ request()->routeIs('profile.edit') ? 'ring-blue-600 dark:ring-blue-400' : 'ring-gray-300 dark:ring-gray-600' }}" src="{{ Auth::user()->profile_photo_url }}" alt="Profil">
        </span>
        <span class="bottom-nav-label">Profil</span>
    </a>
</div>

<!-- Spacer agar konten tidak tertutup bottom nav -->
<div class="bottom-nav-spacer sm:hidden"></div>

<style>
/* ===== BOTTOM NAV ===== */
/* Hanya tampil di mobile (< 640px) */
@media (min-width: 640px) {
    .bottom-nav,
    .bottom-nav-spacer {
        display: none !important;
    }
}

.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 999;
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(20px) saturate(1.8);
    -webkit-backdrop-filter: blur(20px) saturate(1.8);
    border-top: 1px solid rgba(0,0,0,0.07);
    display: flex;
    align-items: stretch;
    height: 64px;
    padding-bottom: env(safe-area-inset-bottom, 0px);
    box-shadow: 0 -4px 24px rgba(0,0,0,0.07);
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .bottom-nav {
        background: rgba(17, 24, 39, 0.9);
        border-top-color: rgba(255,255,255,0.06);
        box-shadow: 0 -4px 24px rgba(0,0,0,0.3);
    }
}
.dark .bottom-nav {
    background: rgba(17, 24, 39, 0.9);
    border-top-color: rgba(255,255,255,0.06);
    box-shadow: 0 -4px 24px rgba(0,0,0,0.3);
}

.bottom-nav-form {
    flex: 1;
    display: flex;
    align-items: stretch;
    margin: 0;
    padding: 0;
}

.bottom-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px 4px;
    text-decoration: none;
    color: #94a3b8;
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: color 0.2s ease, transform 0.15s ease;
    position: relative;
    -webkit-tap-highlight-color: transparent;
    width: 100%;
}

.bottom-nav-item:active {
    transform: scale(0.92);
}

.bottom-nav-item.active {
    color: #2563eb;
}

.dark .bottom-nav-item,
@media (prefers-color-scheme: dark) {
    .bottom-nav-item {
        color: #64748b;
    }
}
.dark .bottom-nav-item.active {
    color: #60a5fa;
}

.bottom-nav-icon {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bottom-nav-icon svg {
    width: 22px;
    height: 22px;
    transition: stroke 0.2s ease;
}

/* Pill indicator di atas icon aktif */
.bottom-nav-item.active .bottom-nav-icon::before {
    content: '';
    position: absolute;
    top: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 28px;
    height: 3px;
    background: #2563eb;
    border-radius: 0 0 4px 4px;
}
.dark .bottom-nav-item.active .bottom-nav-icon::before {
    background: #60a5fa;
}

/* Glow efek untuk icon aktif */
.bottom-nav-item.active .bottom-nav-icon svg {
    filter: drop-shadow(0 0 6px rgba(37, 99, 235, 0.4));
}
.dark .bottom-nav-item.active .bottom-nav-icon svg {
    filter: drop-shadow(0 0 6px rgba(96, 165, 250, 0.4));
}

.bottom-nav-label {
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.01em;
    line-height: 1;
}

/* Spacer agar konten tidak tertutup oleh bottom nav */
.bottom-nav-spacer {
    height: calc(64px + env(safe-area-inset-bottom, 0px));
}

/* Animasi masuk */
@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
.bottom-nav {
    animation: slideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}
</style>

@once
@push('scripts')
<style>
/* ===== SWEETALERT2 – HADIRIN THEME ===== */

/* Popup card */
.swal-hadirin-popup {
    font-family: 'Inter', ui-sans-serif, system-ui, sans-serif !important;
    border-radius: 20px !important;
    padding: 28px 24px 24px !important;
    border: 1px solid rgba(37, 99, 235, 0.15) !important;
    box-shadow: 0 8px 40px rgba(15, 23, 42, 0.15), 0 2px 8px rgba(37, 99, 235, 0.08) !important;
    max-width: 340px !important;
    width: calc(100% - 32px) !important;
}

/* Icon override – warna warning jadi biru sesuai tema */
.swal-hadirin-popup .swal2-warning {
    border-color: #f59e0b !important;
    color: #f59e0b !important;
}

/* Judul */
.swal-hadirin-title {
    font-size: 17px !important;
    font-weight: 700 !important;
    letter-spacing: -0.3px !important;
    line-height: 1.3 !important;
    padding: 0 !important;
    margin-bottom: 8px !important;
}

/* Teks */
.swal-hadirin-text {
    font-size: 13px !important;
    font-weight: 400 !important;
    color: #64748b !important;
    padding: 0 !important;
    margin: 0 0 20px !important;
    line-height: 1.5 !important;
}

/* Actions area */
.swal-hadirin-popup .swal2-actions {
    gap: 10px !important;
    flex-direction: row !important;
    margin: 0 !important;
    width: 100% !important;
}

/* Tombol Konfirmasi (Keluar) */
.swal-hadirin-confirm {
    background: #2563eb !important;
    color: #fff !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    border-radius: 12px !important;
    padding: 10px 20px !important;
    border: none !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35) !important;
    transition: all 0.18s ease !important;
    flex: 1 !important;
}
.swal-hadirin-confirm:hover {
    background: #1d4ed8 !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45) !important;
    transform: translateY(-1px) !important;
}
.swal-hadirin-confirm:active {
    transform: translateY(0) scale(0.97) !important;
}
.swal-hadirin-confirm:focus {
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
}

/* Tombol Batal */
.swal-hadirin-cancel {
    background: transparent !important;
    color: #64748b !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    border-radius: 12px !important;
    padding: 10px 20px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: none !important;
    transition: all 0.18s ease !important;
    flex: 1 !important;
}
.swal-hadirin-cancel:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #475569 !important;
}
.swal-hadirin-cancel:focus {
    box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.15) !important;
}

/* ===== DARK MODE ===== */
html.dark .swal-hadirin-popup,
.swal2-popup.swal-hadirin-popup.dark-popup {
    border-color: rgba(59, 130, 246, 0.2) !important;
    box-shadow: 0 8px 40px rgba(0,0,0,0.4), 0 2px 8px rgba(59, 130, 246, 0.1) !important;
    background: #1e293b !important;
    color: #e2e8f0 !important;
}
html.dark .swal-hadirin-text,
.swal2-popup.swal-hadirin-popup.dark-popup .swal-hadirin-text {
    color: #94a3b8 !important;
}
html.dark .swal-hadirin-confirm,
.swal2-popup.swal-hadirin-popup.dark-popup ~ .swal2-actions .swal-hadirin-confirm {
    background: #3b82f6 !important;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3) !important;
}
html.dark .swal-hadirin-confirm:hover {
    background: #2563eb !important;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4) !important;
}
html.dark .swal-hadirin-cancel {
    border-color: #2d3f55 !important;
    color: #94a3b8 !important;
}
html.dark .swal-hadirin-cancel:hover {
    background: #162032 !important;
    border-color: #3d5a7a !important;
    color: #e2e8f0 !important;
}

/* Fallback: prefers-color-scheme dark (untuk yang tidak pakai class toggle) */
@media (prefers-color-scheme: dark) {
    .swal-hadirin-popup {
        border-color: rgba(59, 130, 246, 0.2) !important;
        box-shadow: 0 8px 40px rgba(0,0,0,0.4), 0 2px 8px rgba(59, 130, 246, 0.1) !important;
    }
    .swal-hadirin-text {
        color: #94a3b8 !important;
    }
    .swal-hadirin-cancel {
        border-color: #2d3f55 !important;
        color: #94a3b8 !important;
    }
    .swal-hadirin-cancel:hover {
        background: #162032 !important;
        border-color: #3d5a7a !important;
        color: #e2e8f0 !important;
    }
}

/* Animasi masuk */
@keyframes swalSlideUp {
    from { transform: translateY(20px) scale(0.96); opacity: 0; }
    to   { transform: translateY(0)     scale(1);    opacity: 1; }
}
.swal-animate-in {
    animation: swalSlideUp 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) both !important;
}
.swal-animate-out {
    animation: swalSlideUp 0.18s ease reverse both !important;
}


</style>

<script>
(function () {
    function showLogoutSwal(formId) {
        var isDark = document.documentElement.classList.contains('dark')
                  || window.matchMedia('(prefers-color-scheme: dark)').matches;
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: 'Sesi Anda akan diakhiri dan Anda akan diarahkan ke halaman login.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup:         'swal-hadirin-popup',
                title:         'swal-hadirin-title',
                htmlContainer: 'swal-hadirin-text',
                confirmButton: 'swal-hadirin-confirm',
                cancelButton:  'swal-hadirin-cancel',
            },
            background: isDark ? '#1e293b' : '#ffffff',
            color:      isDark ? '#e2e8f0' : '#0f172a',
            showClass:  { popup: 'swal-animate-in' },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function initLogoutSwal() {
        // Desktop dropdown logout
        var btnDesktop = document.getElementById('bnav-logout-desktop');
        if (btnDesktop && !btnDesktop._swalBound) {
            btnDesktop._swalBound = true;
            btnDesktop.addEventListener('click', function () {
                showLogoutSwal('logoutFormDesktop');
            });
        }

        // Profile page logout button
        var btnProfile = document.getElementById('btn-profile-logout');
        if (btnProfile && !btnProfile._swalBound) {
            btnProfile._swalBound = true;
            btnProfile.addEventListener('click', function () {
                showLogoutSwal('logoutFormDesktop');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLogoutSwal);
    } else {
        initLogoutSwal();
    }
})();
</script>
@endpush
@endonce
