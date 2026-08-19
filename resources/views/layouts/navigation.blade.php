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

                    @if (Auth::user()->isAdmin())


                        <x-nav-link :href="route('admin.absensi')" :active="request()->routeIs('admin.absensi*')">
                            {{ __('Absensi') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.pengajuan-izin')" :active="request()->routeIs('admin.pengajuan-izin*')">
                            {{ __('Pengajuan Izin') }}
                        </x-nav-link>

                        <!-- Dropdown Master Data -->
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="56" contentClasses="py-2 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/60 backdrop-blur-md w-35">
                                <x-slot name="trigger">
                                    <button type="button" class="inline-flex items-center gap-1.5 px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out h-16 {{ request()->routeIs('admin.master.*') ? 'border-blue-600 dark:border-blue-400 text-gray-900 dark:text-gray-100 font-semibold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}">
                                        <span>Master Data</span>
                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                        Pilihan Master
                                    </div>

                                    <!-- Divisi -->
                                    <a href="{{ route('admin.master.divisi') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/60 hover:text-blue-600 dark:hover:text-blue-400 transition rounded-xl mx-1.5 {{ request()->routeIs('admin.master.divisi') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/80 dark:bg-gray-700/60 font-semibold' : '' }}">
                                        <span>Divisi</span>
                                    </a>

                                    <!-- Data Admin -->
                                    <a href="{{ route('admin.master.admin') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/60 hover:text-blue-600 dark:hover:text-blue-400 transition rounded-xl mx-1.5 {{ request()->routeIs('admin.master.admin') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/80 dark:bg-gray-700/60 font-semibold' : '' }}">
                                        <span>Data Admin</span>
                                    </a>

                                    <!-- Data User -->
                                    <a href="{{ route('admin.master.user') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/60 hover:text-blue-600 dark:hover:text-blue-400 transition rounded-xl mx-1.5 {{ request()->routeIs('admin.master.user') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/80 dark:bg-gray-700/60 font-semibold' : '' }}">
                                        <span>Data User</span>
                                    </a>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <x-nav-link :href="route('admin.export')" :active="request()->routeIs('admin.export*')">
                            {{ __('Export') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right Actions (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-6">
                <!-- Theme Toggle Button -->
                <button type="button"
                    @click="$store.darkMode.toggle()"
                    class="p-2 rounded-xl text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60 border border-transparent hover:border-gray-200 dark:hover:border-gray-700 focus:outline-none transition-all duration-200"
                    aria-label="Toggle dark mode"
                    title="Ganti Mode Gelap / Terang">
                    <svg x-show="$store.darkMode.on" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
                    </svg>
                    <svg x-show="!$store.darkMode.on" class="h-5 w-5 " xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                </button>

                <!-- Settings Dropdown (Desktop) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <img class="h-8 w-8 rounded-full object-cover ring-2 ring-blue-500/20 dark:ring-blue-400/20" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->username }}">
                            <div class="flex items-center gap-1.5">
                                <span class="font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->username }}</span>
                                @if (Auth::user()->isAdmin())
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300">Admin</span>
                                @endif
                            </div>

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

            <!-- Mobile: Dark mode toggle di kanan -->
            <div class="flex items-center gap-1 sm:hidden">
                <!-- Theme Toggle Button (Mobile) -->
                <button type="button"
                    @click="$store.darkMode.toggle()"
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60 focus:outline-none transition"
                    aria-label="Toggle dark mode">
                    <svg x-show="$store.darkMode.on" class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
                    </svg>
                    <svg x-show="!$store.darkMode.on" class="h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Hidden form untuk logout -->
<form method="POST" action="{{ route('logout') }}" id="logoutFormDesktop" class="hidden">
    @csrf
</form>

<!-- ===== BOTTOM NAVIGATION BAR (Mobile Only) ===== -->
<div class="bottom-nav sm:hidden" id="bottomNav" x-data="{ masterSheetOpen: false }">
    <!-- Mobile Master Data Sheet Popover -->
    <div x-show="masterSheetOpen"
         @click.outside="masterSheetOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="absolute bottom-[72px] left-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border border-gray-200/80 dark:border-gray-700/80 rounded-2xl shadow-2xl p-3 z-[1000]"
         style="display: none;">
        <div class="flex items-center justify-between px-2 pb-2 mb-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pilih Master Data</span>
            <button type="button" @click="masterSheetOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="space-y-1">
            <a href="{{ route('admin.master.divisi') }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/60 {{ request()->routeIs('admin.master.divisi') ? 'bg-blue-50 dark:bg-gray-700/60 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                <div class="w-8 h-8 rounded-lg bg-purple-500/15 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="text-left">
                    <div class="text-sm font-semibold">Divisi</div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">Master divisi / departemen</div>
                </div>
            </a>
            <a href="{{ route('admin.master.admin') }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/60 {{ request()->routeIs('admin.master.admin') ? 'bg-blue-50 dark:bg-gray-700/60 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                <div class="w-8 h-8 rounded-lg bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="text-left">
                    <div class="text-sm font-semibold">Data Admin</div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">Master akun administrator</div>
                </div>
            </a>
            <a href="{{ route('admin.master.user') }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/60 {{ request()->routeIs('admin.master.user') ? 'bg-blue-50 dark:bg-gray-700/60 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="text-left">
                    <div class="text-sm font-semibold">Data User</div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">Master akun karyawan / user</div>
                </div>
            </a>
        </div>
    </div>

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
        <span class="bottom-nav-label">Dashboard</span>
    </a>

    @if (Auth::user()->isAdmin())
        {{-- Master Data Trigger --}}
        <button type="button"
           @click="masterSheetOpen = !masterSheetOpen"
           class="bottom-nav-item {{ request()->routeIs('admin.master.*') ? 'active' : '' }}"
           id="bnav-master">
            <span class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
            </span>
            <span class="bottom-nav-label">Master</span>
        </button>

        {{-- Absensi --}}
        <a href="{{ route('admin.absensi') }}"
           class="bottom-nav-item {{ request()->routeIs('admin.absensi*') ? 'active' : '' }}"
           id="bnav-absensi">
            <span class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <path d="M9 16l2 2 4-4"></path>
                </svg>
            </span>
            <span class="bottom-nav-label">Absensi</span>
        </a>

        {{-- Pengajuan Izin --}}
        <a href="{{ route('admin.pengajuan-izin') }}"
           class="bottom-nav-item {{ request()->routeIs('admin.pengajuan-izin*') ? 'active' : '' }}"
           id="bnav-izin">
            <span class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </span>
            <span class="bottom-nav-label">Izin</span>
        </a>

        {{-- Export --}}
        <a href="{{ route('admin.export') }}"
           class="bottom-nav-item {{ request()->routeIs('admin.export*') ? 'active' : '' }}"
           id="bnav-export">
            <span class="bottom-nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
            </span>
            <span class="bottom-nav-label">Export</span>
        </a>
    @endif

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

/* Dark mode (dikontrol oleh class .dark) */
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
    gap: 3px;
    padding: 6px 2px;
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
    min-width: 0;
}

.bottom-nav-item:active {
    transform: scale(0.92);
}

.bottom-nav-item.active {
    color: #2563eb;
}

.dark .bottom-nav-item {
    color: #64748b;
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
    width: 20px;
    height: 20px;
    transition: stroke 0.2s ease;
}

/* Pill indicator di atas icon aktif */
.bottom-nav-item.active .bottom-nav-icon::before {
    content: '';
    position: absolute;
    top: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 22px;
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
    font-size: 9.5px;
    font-weight: 500;
    letter-spacing: -0.01em;
    line-height: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
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
window.toggleAppTheme = function () {
    var isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('hadirin-theme', isDark ? 'dark' : 'light');
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: isDark ? 'dark' : 'light' } }));
};

(function () {
    function showLogoutSwal(formId) {
        var isDark = document.documentElement.classList.contains('dark');
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
