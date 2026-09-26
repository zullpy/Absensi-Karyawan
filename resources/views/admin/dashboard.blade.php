<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Selamat datang kembali, <span class="font-semibold text-blue-600 dark:text-blue-400">{{ Auth::user()->username }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">
                    Panel Admin
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- KPI Cards Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <!-- Total Karyawan -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Total Karyawan</span>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $totalEmployees }}</div>
                        <span class="text-[11px] text-gray-400">Role User Aktif</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Hadir Hari Ini -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Hadir Hari Ini</span>
                        <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-0.5">{{ $todayAttendancesCount }}</div>
                        <span class="text-[11px] text-gray-400">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Izin Menunggu -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-amber-600 dark:text-amber-400">Izin Menunggu</span>
                        <div class="text-2xl font-bold text-amber-700 dark:text-amber-400 mt-0.5">{{ $pendingLeavesCount }}</div>
                        <span class="text-[11px] text-gray-400">Perlu Persetujuan</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Quick Access Modules -->
            <div class="bg-white dark:bg-[#1e293b] rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Navigasi Modul Admin</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    <a href="{{ route('admin.absensi') }}" class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 dark:bg-[#0f172a] hover:bg-blue-50 dark:hover:bg-blue-900/30 border border-gray-200 dark:border-slate-700 transition text-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Data Absensi</span>
                    </a>

                    <a href="{{ route('admin.pengajuan-izin') }}" class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 dark:bg-[#0f172a] hover:bg-amber-50 dark:hover:bg-amber-900/30 border border-gray-200 dark:border-slate-700 transition text-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Pengajuan Izin</span>
                    </a>

                    <a href="{{ route('admin.master.user') }}" class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 dark:bg-[#0f172a] hover:bg-emerald-50 dark:hover:bg-emerald-900/30 border border-gray-200 dark:border-slate-700 transition text-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Data User</span>
                    </a>

                    <a href="{{ route('admin.master.divisi') }}" class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 dark:bg-[#0f172a] hover:bg-purple-50 dark:hover:bg-purple-900/30 border border-gray-200 dark:border-slate-700 transition text-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Data Divisi</span>
                    </a>

                    <a href="{{ route('admin.export') }}" class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 dark:bg-[#0f172a] hover:bg-indigo-50 dark:hover:bg-indigo-900/30 border border-gray-200 dark:border-slate-700 transition text-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Export Data</span>
                    </a>
                </div>
            </div>

            <!-- Two Columns: Today's Attendance & Pending Leaves -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Today's Attendance Activity -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Absensi Hari Ini</h3>
                        <a href="{{ route('admin.absensi') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Lihat Semua &rarr;</a>
                    </div>
                    @if ($recentAttendances->count() > 0)
                        <div class="divide-y divide-gray-200 dark:divide-slate-700 text-xs">
                            @foreach ($recentAttendances as $att)
                                <div class="py-2.5 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $att->user?->profile_photo_url }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $att->user?->username }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($att->time_in)->format('H:i') }} WIB</div>
                                        <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded {{ $att->status === 'late' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
                                            {{ $att->status === 'late' ? 'Terlambat' : 'Tepat Waktu' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center text-gray-400 dark:text-gray-500 text-xs">
                            Belum ada absensi karyawan hari ini.
                        </div>
                    @endif
                </div>

                <!-- Pending Leave Applications -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pengajuan Izin Menunggu</h3>
                        <a href="{{ route('admin.pengajuan-izin') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Lihat Semua &rarr;</a>
                    </div>
                    @if ($recentLeaves->count() > 0)
                        <div class="divide-y divide-gray-200 dark:divide-slate-700 text-xs">
                            @foreach ($recentLeaves as $leave)
                                <div class="py-2.5 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $leave->user?->profile_photo_url }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $leave->user?->username }}</div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ ucfirst($leave->type) }} ({{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d/m') }} - {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d/m') }})
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.pengajuan-izin') }}" class="px-2.5 py-1 rounded text-xs font-semibold bg-amber-600 text-white hover:bg-amber-700 transition">
                                        Tinjau
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center text-gray-400 dark:text-gray-500 text-xs">
                            Tidak ada permohonan izin yang sedang menunggu.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
