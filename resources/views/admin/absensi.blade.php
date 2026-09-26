<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Data Absensi Karyawan') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Pantau rekapitulasi kehadiran, foto selfie verifikasi, dan titik lokasi presensi karyawan.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                    Panel Admin
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8"
        x-data="{
            detailModalOpen: false,
            photoModalOpen: false,
            photoModalUrl: '',
            photoModalTitle: '',
            selectedAttendance: null,
            openDetail(data) {
                this.selectedAttendance = data;
                this.detailModalOpen = true;
            },
            openPhoto(url, title) {
                if (!url) return;
                this.photoModalUrl = url;
                this.photoModalTitle = title;
                this.photoModalOpen = true;
            },
            confirmDelete(url, name, date) {
                Swal.fire({
                    title: 'Hapus Absensi?',
                    text: `Apakah Anda yakin ingin menghapus catatan absensi ${name} tanggal ${date}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteAttendanceForm');
                        form.action = url;
                        form.submit();
                    }
                });
            }
        }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Alerts -->
            @if (session('success'))
                <div class="p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div><span class="font-bold">Berhasil:</span> {{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-3.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    <div><span class="font-bold">Error:</span> {{ session('error') }}</div>
                </div>
            @endif

            <!-- KPI Cards Overview -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Total Absensi -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Absensi</span>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $totalCount }}</div>
                        <span class="text-[11px] text-gray-400">{{ $date ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y') : 'Semua Rekap' }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>

                <!-- Tepat Waktu -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Tepat Waktu</span>
                        <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-0.5">{{ $presentCount }}</div>
                        <span class="text-[11px] text-gray-400">Masuk &le; 08:30 WIB</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Terlambat -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">Terlambat</span>
                        <div class="text-2xl font-bold text-red-700 dark:text-red-400 mt-0.5">{{ $lateCount }}</div>
                        <span class="text-[11px] text-gray-400">Masuk &gt; 08:30 WIB</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Belum Pulang -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-sky-600 dark:text-sky-400 font-medium">Belum Pulang</span>
                        <div class="text-2xl font-bold text-sky-700 dark:text-sky-400 mt-0.5">{{ $notClockedOutCount }}</div>
                        <span class="text-[11px] text-gray-400">Belum Clock-Out</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Main Container -->
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">

                <!-- Filter & Search Toolbar -->
                <div class="p-3.5 sm:p-4 border-b border-gray-100 dark:border-slate-700/80">
                    <form method="GET" action="{{ route('admin.absensi') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        
                        <!-- Search Box (Left) -->
                        <div class="relative w-full lg:w-72">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama karyawan / no HP..."
                                class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 rounded-lg pl-8 pr-3 py-1.5 text-xs text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Filters & Quick Actions (Right) -->
                        <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            <!-- Tanggal Picker -->
                            <div class="inline-flex items-center gap-1.5 bg-gray-50 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <input type="date" name="date" value="{{ $date ?? '' }}" onchange="this.form.submit()"
                                    class="bg-transparent border-0 p-0 text-xs text-gray-800 dark:text-gray-200 focus:ring-0 cursor-pointer">
                            </div>

                            @if (!$date || $date !== \Carbon\Carbon::today()->toDateString())
                                <a href="{{ route('admin.absensi', array_merge(request()->except(['date', 'page']), ['date' => \Carbon\Carbon::today()->toDateString()])) }}"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-medium border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-[#0f172a] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                                    Hari Ini
                                </a>
                            @endif

                            <!-- Divisi -->
                            <select name="division_id" onchange="this.form.submit()"
                                class="bg-gray-50 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition cursor-pointer">
                                <option value="">Semua Divisi</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div->id }}" {{ ($divisionId ?? '') == $div->id ? 'selected' : '' }}>
                                        {{ $div->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Status (Combined) -->
                            <select name="status" onchange="this.form.submit()"
                                class="bg-gray-50 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="present" {{ ($status ?? '') === 'present' ? 'selected' : '' }}>Tepat Waktu</option>
                                <option value="late" {{ ($status ?? '') === 'late' ? 'selected' : '' }}>Terlambat</option>
                                <option value="not_clocked_out" {{ ($status ?? '') === 'not_clocked_out' ? 'selected' : '' }}>Belum Pulang</option>
                                <option value="clocked_out" {{ ($status ?? '') === 'clocked_out' ? 'selected' : '' }}>Sudah Pulang</option>
                            </select>

                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 transition shadow-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Cari</span>
                            </button>

                            @if ($search || $date || $divisionId || $status || $clockOutStatus)
                                <a href="{{ route('admin.absensi') }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition"
                                    title="Reset filter">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Content (Desktop) -->
                @if ($attendances->count() > 0)
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-slate-700 bg-gray-50/75 dark:bg-[#0f172a]/50 text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider text-[11px]">
                                    <th class="px-4 py-3">Karyawan</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Absen Masuk</th>
                                    <th class="px-4 py-3">Absen Pulang</th>
                                    <th class="px-4 py-3">Status & Jarak</th>
                                    <th class="px-4 py-3">Lokasi GPS</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/60">
                                @foreach ($attendances as $item)
                                    @php
                                        $user = $item->user;
                                        $photoInUrl = $item->photo_in_url;
                                        $photoOutUrl = $item->photo_out_url;
                                        $jsonData = json_encode([
                                            'id' => $item->id,
                                            'username' => $user->username ?? 'Unknown',
                                            'no_hp' => $user->no_hp ?? '-',
                                            'division' => $user->division->name ?? 'Belum Diatur',
                                            'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y'),
                                            'time_in' => $item->time_in ? \Carbon\Carbon::parse($item->time_in)->format('H:i:s') : '-',
                                            'time_out' => $item->time_out ? \Carbon\Carbon::parse($item->time_out)->format('H:i:s') : '-',
                                            'status' => $item->status,
                                            'distance' => $item->distance_in_meters,
                                            'lat_in' => $item->lat_in,
                                            'long_in' => $item->long_in,
                                            'lat_out' => $item->lat_out,
                                            'long_out' => $item->long_out,
                                            'photo_in' => $photoInUrl,
                                            'photo_out' => $photoOutUrl,
                                            'shift' => $item->shift->name ?? null,
                                        ]);
                                    @endphp
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-800/40 transition">
                                        <!-- Karyawan -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=User' }}"
                                                    alt="Photo"
                                                    class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0">
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                        <span>{{ $user->username ?? 'Unknown' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        @if ($user && $user->division)
                                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                                {{ $user->division->name }}
                                                            </span>
                                                        @endif
                                                        <span class="text-gray-400 font-mono text-[11px]">{{ $user->no_hp ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Tanggal -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-semibold text-gray-800 dark:text-gray-200">
                                                {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="text-[11px] text-gray-400">
                                                {{ \Carbon\Carbon::parse($item->date)->translatedFormat('l') }}
                                                @if ($item->shift)
                                                    &bull; <span class="text-blue-500">{{ $item->shift->name }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Absen Masuk -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                @if ($photoInUrl)
                                                    <button type="button"
                                                        @click="openPhoto('{{ $photoInUrl }}', 'Selfie Masuk - {{ $user->username ?? 'User' }}')"
                                                        class="relative group w-9 h-9 rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 shrink-0 cursor-pointer shadow-xs">
                                                        <img src="{{ $photoInUrl }}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                        </div>
                                                    </button>
                                                @else
                                                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-gray-400 shrink-0">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="font-mono font-bold text-gray-900 dark:text-gray-100 text-xs">
                                                        {{ $item->time_in ? \Carbon\Carbon::parse($item->time_in)->format('H:i:s') : '-' }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 block">WIB</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Absen Pulang -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($item->time_out)
                                                <div class="flex items-center gap-2.5">
                                                    @if ($photoOutUrl)
                                                        <button type="button"
                                                            @click="openPhoto('{{ $photoOutUrl }}', 'Selfie Pulang - {{ $user->username ?? 'User' }}')"
                                                            class="relative group w-9 h-9 rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 shrink-0 cursor-pointer shadow-xs">
                                                            <img src="{{ $photoOutUrl }}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                            </div>
                                                        </button>
                                                    @else
                                                        <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-gray-400 shrink-0">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <span class="font-mono font-bold text-gray-900 dark:text-gray-100 text-xs">
                                                            {{ \Carbon\Carbon::parse($item->time_out)->format('H:i:s') }}
                                                        </span>
                                                        <span class="text-[10px] text-gray-400 block">WIB</span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600 dark:bg-slate-700/60 dark:text-gray-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    Belum Pulang
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status & Jarak -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="space-y-1">
                                                @if ($item->status === 'present')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                        Tepat Waktu
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                        Terlambat
                                                    </span>
                                                @endif

                                                @if ($item->distance_in_meters !== null)
                                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                                                        <span>{{ round($item->distance_in_meters) }} m</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Lokasi GPS -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($item->lat_in && $item->long_in)
                                                <a href="https://www.google.com/maps?q={{ $item->lat_in }},{{ $item->long_in }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span>Lihat Maps</span>
                                                </a>
                                            @else
                                                <span class="text-gray-400 italic text-[11px]">Tidak ada koordinat</span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-4 py-3 whitespace-nowrap text-right space-x-2">
                                            <button type="button"
                                                @click='openDetail({{ $jsonData }})'
                                                class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">
                                                Detail
                                            </button>
                                            <button type="button"
                                                @click="confirmDelete('{{ route('admin.absensi.destroy', $item->id) }}', '{{ $user->username ?? 'User' }}', '{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}')"
                                                class="text-red-600 dark:text-red-400 font-semibold hover:underline cursor-pointer">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards List -->
                    <div class="block md:hidden divide-y divide-gray-200 dark:divide-slate-700 p-4 space-y-4">
                        @foreach ($attendances as $item)
                            @php
                                $user = $item->user;
                                $photoInUrl = $item->photo_in_url;
                                $photoOutUrl = $item->photo_out_url;
                                $jsonData = json_encode([
                                    'id' => $item->id,
                                    'username' => $user->username ?? 'Unknown',
                                    'no_hp' => $user->no_hp ?? '-',
                                    'division' => $user->division->name ?? 'Belum Diatur',
                                    'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y'),
                                    'time_in' => $item->time_in ? \Carbon\Carbon::parse($item->time_in)->format('H:i:s') : '-',
                                    'time_out' => $item->time_out ? \Carbon\Carbon::parse($item->time_out)->format('H:i:s') : '-',
                                    'status' => $item->status,
                                    'distance' => $item->distance_in_meters,
                                    'lat_in' => $item->lat_in,
                                    'long_in' => $item->long_in,
                                    'lat_out' => $item->lat_out,
                                    'long_out' => $item->long_out,
                                    'photo_in' => $photoInUrl,
                                    'photo_out' => $photoOutUrl,
                                    'shift' => $item->shift->name ?? null,
                                ]);
                            @endphp
                            <div class="pt-4 first:pt-0 space-y-2.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=User' }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $user->username ?? 'Unknown' }}</div>
                                            <div class="text-gray-500 font-mono text-[11px]">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}</div>
                                        </div>
                                    </div>
                                    @if ($item->status === 'present')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            Tepat Waktu
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                            Terlambat
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-lg bg-gray-50 dark:bg-[#0f172a] text-[11px]">
                                    <div>
                                        <span class="text-gray-400 block">Masuk:</span>
                                        <span class="font-bold font-mono text-gray-800 dark:text-gray-200">
                                            {{ $item->time_in ? \Carbon\Carbon::parse($item->time_in)->format('H:i') : '-' }} WIB
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block">Pulang:</span>
                                        <span class="font-bold font-mono text-gray-800 dark:text-gray-200">
                                            {{ $item->time_out ? \Carbon\Carbon::parse($item->time_out)->format('H:i') . ' WIB' : 'Belum Pulang' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-1">
                                    @if ($item->lat_in && $item->long_in)
                                        <a href="https://www.google.com/maps?q={{ $item->lat_in }},{{ $item->long_in }}" target="_blank"
                                            class="text-blue-600 dark:text-blue-400 font-medium inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            <span>Maps</span>
                                        </a>
                                    @else
                                        <div></div>
                                    @endif
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click='openDetail({{ $jsonData }})' class="text-blue-600 dark:text-blue-400 font-semibold underline cursor-pointer">
                                            Detail
                                        </button>
                                        <button type="button" @click="confirmDelete('{{ route('admin.absensi.destroy', $item->id) }}', '{{ $user->username ?? 'User' }}', '{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}')" class="text-red-600 dark:text-red-400 font-semibold underline cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($attendances->hasPages())
                        <div class="p-3.5 border-t border-gray-200 dark:border-slate-700">
                            {{ $attendances->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-slate-700/60 text-gray-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Tidak ada data absensi</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            @if ($search || $date || $divisionId || $status || $clockOutStatus)
                                Tidak ada data absensi yang cocok dengan kriteria filter saat ini. Coba ubah atau reset filter.
                            @else
                                Belum ada karyawan yang melakukan absensi pada sistem.
                            @endif
                        </p>
                        @if ($search || $date || $divisionId || $status || $clockOutStatus)
                            <a href="{{ route('admin.absensi') }}" class="inline-flex items-center gap-1 mt-3 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 transition">
                                Reset Semua Filter
                            </a>
                        @endif
                    </div>
                @endif

            </div>

        </div>

        <!-- Detail Modal (Alpine.js) -->
        <div x-show="detailModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75"
            style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-gray-200 dark:border-slate-700"
                @click.outside="detailModalOpen = false">
                
                <div class="p-4 sm:p-5 flex items-center justify-between border-b border-gray-200 dark:border-slate-700 bg-gray-50/75 dark:bg-[#0f172a]/50">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Detail Presensi Karyawan</span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedAttendance ? selectedAttendance.username + ' • ' + selectedAttendance.date : ''"></p>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <template x-if="selectedAttendance">
                    <div class="p-5 space-y-4 text-xs max-h-[80vh] overflow-y-auto">
                        
                        <!-- Employee Info Banner -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700">
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-white" x-text="selectedAttendance.username"></div>
                                <div class="text-gray-400 text-xs mt-0.5">
                                    <span x-text="'Divisi: ' + selectedAttendance.division"></span> &bull; 
                                    <span x-text="'HP: ' + selectedAttendance.no_hp"></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                :class="selectedAttendance.status === 'present' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300'"
                                x-text="selectedAttendance.status === 'present' ? 'Tepat Waktu' : 'Terlambat'">
                            </span>
                        </div>

                        <!-- Photos Grid: Selfie Masuk & Selfie Keluar -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Selfie Masuk -->
                            <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 bg-white dark:bg-slate-800/40">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Foto Selfie Masuk</span>
                                <template x-if="selectedAttendance.photo_in">
                                    <img :src="selectedAttendance.photo_in" class="w-full h-36 object-cover rounded-lg border border-gray-200 dark:border-slate-700 shadow-xs cursor-pointer hover:opacity-90"
                                        @click="openPhoto(selectedAttendance.photo_in, 'Selfie Masuk - ' + selectedAttendance.username)">
                                </template>
                                <template x-if="!selectedAttendance.photo_in">
                                    <div class="w-full h-36 rounded-lg bg-gray-100 dark:bg-slate-700/60 flex items-center justify-center text-gray-400">
                                        Tidak ada foto
                                    </div>
                                </template>
                                <div class="mt-2 text-center font-mono font-bold text-gray-800 dark:text-gray-200 text-xs" x-text="'Masuk: ' + selectedAttendance.time_in + ' WIB'"></div>
                            </div>

                            <!-- Selfie Pulang -->
                            <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 bg-white dark:bg-slate-800/40">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Foto Selfie Pulang</span>
                                <template x-if="selectedAttendance.photo_out">
                                    <img :src="selectedAttendance.photo_out" class="w-full h-36 object-cover rounded-lg border border-gray-200 dark:border-slate-700 shadow-xs cursor-pointer hover:opacity-90"
                                        @click="openPhoto(selectedAttendance.photo_out, 'Selfie Pulang - ' + selectedAttendance.username)">
                                </template>
                                <template x-if="!selectedAttendance.photo_out">
                                    <div class="w-full h-36 rounded-lg bg-gray-100 dark:bg-slate-700/60 flex flex-col items-center justify-center text-gray-400 text-center p-2">
                                        <svg class="w-6 h-6 mb-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                        <span>Belum Absen Pulang</span>
                                    </div>
                                </template>
                                <div class="mt-2 text-center font-mono font-bold text-gray-800 dark:text-gray-200 text-xs" x-text="'Pulang: ' + (selectedAttendance.time_out !== '-' ? selectedAttendance.time_out + ' WIB' : 'Belum')"></div>
                            </div>
                        </div>

                        <!-- Technical Details Grid -->
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 divide-y divide-gray-200 dark:divide-slate-700 overflow-hidden">
                            <div class="p-2.5 flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Jarak Dari Kantor</span>
                                <span class="font-bold text-gray-900 dark:text-white" x-text="selectedAttendance.distance ? Math.round(selectedAttendance.distance) + ' Meter' : '-'"></span>
                            </div>
                            <div class="p-2.5 flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Koordinat Masuk</span>
                                <span class="font-mono text-gray-900 dark:text-white" x-text="(selectedAttendance.lat_in && selectedAttendance.long_in) ? selectedAttendance.lat_in + ', ' + selectedAttendance.long_in : '-'"></span>
                            </div>
                            <template x-if="selectedAttendance.lat_out && selectedAttendance.long_out">
                                <div class="p-2.5 flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Koordinat Pulang</span>
                                    <span class="font-mono text-gray-900 dark:text-white" x-text="selectedAttendance.lat_out + ', ' + selectedAttendance.long_out"></span>
                                </div>
                            </template>
                            <template x-if="selectedAttendance.shift">
                                <div class="p-2.5 flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Shift Kerja</span>
                                    <span class="font-bold text-blue-600 dark:text-blue-400" x-text="selectedAttendance.shift"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Maps Button -->
                        <template x-if="selectedAttendance.lat_in && selectedAttendance.long_in">
                            <a :href="'https://www.google.com/maps?q=' + selectedAttendance.lat_in + ',' + selectedAttendance.long_in"
                                target="_blank"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Buka Lokasi di Google Maps</span>
                            </a>
                        </template>

                    </div>
                </template>

                <div class="p-3.5 bg-gray-50 dark:bg-[#0f172a] border-t border-gray-200 dark:border-slate-700 text-right">
                    <button type="button" @click="detailModalOpen = false"
                        class="px-4 py-1.5 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-600 transition cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

        <!-- Lightbox Photo Viewer Modal -->
        <div x-show="photoModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-xs"
            style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl border border-gray-200 dark:border-slate-700"
                @click.outside="photoModalOpen = false">
                
                <div class="p-3.5 flex items-center justify-between border-b border-gray-200 dark:border-slate-700">
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200" x-text="photoModalTitle"></h4>
                    <button type="button" @click="photoModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-3 bg-black/5 dark:bg-black/40 flex items-center justify-center">
                    <img :src="photoModalUrl" class="max-h-[65vh] w-auto rounded-lg object-contain shadow-md">
                </div>

                <div class="p-3 text-right">
                    <button type="button" @click="photoModalOpen = false" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-slate-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-slate-600 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden Delete Form -->
        <form id="deleteAttendanceForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    </div>
</x-app-layout>
