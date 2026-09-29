<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                        {{ __('Pusat Export & Laporan') }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Unduh arsip presensi, pengajuan perizinan, dan rekapitulasi kehadiran bulanan.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                    <span class="w-2 h-2 rounded-full bg-blue-500 me-1.5 animate-pulse"></span>
                    {{ $office->name ?? 'Kantor Utama' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="exportCenter()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Tab Buttons -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-2 shadow-sm border border-gray-100 dark:border-gray-700/60">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <button type="button"
                        @click="setTab('absensi')"
                        :class="activeTab === 'absensi' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/60'"
                        class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Laporan Presensi Harian</span>
                    </button>

                    <button type="button"
                        @click="setTab('izin')"
                        :class="activeTab === 'izin' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/60'"
                        class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Izin & Cuti</span>
                    </button>

                    <button type="button"
                        @click="setTab('rekap')"
                        :class="activeTab === 'rekap' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/60'"
                        class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl font-semibold text-sm transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Rekap Bulanan Karyawan</span>
                    </button>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 1: LAPORAN PRESENSI HARIAN -->
            <!-- ============================================================== -->
            <div x-show="activeTab === 'absensi'" x-cloak class="space-y-6">
                <!-- Filter Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 sm:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-100 dark:border-gray-700/60">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                Filter Kriteria Presensi
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tentukan periode tanggal dan filter data yang ingin diexport.</p>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 me-1">Pintasan:</span>
                            <button type="button" @click="setAbsensiPreset('today')" :class="absensiPreset === 'today' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Hari Ini</button>
                            <button type="button" @click="setAbsensiPreset('yesterday')" :class="absensiPreset === 'yesterday' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Kemarin</button>
                            <button type="button" @click="setAbsensiPreset('last7')" :class="absensiPreset === 'last7' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">7 Hari Terakhir</button>
                            <button type="button" @click="setAbsensiPreset('thisMonth')" :class="absensiPreset === 'thisMonth' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Bulan Ini</button>
                            <button type="button" @click="setAbsensiPreset('lastMonth')" :class="absensiPreset === 'lastMonth' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Bulan Lalu</button>
                        </div>
                    </div>

                    <!-- Filter Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-5">
                        <!-- Start Date -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai</label>
                            <input type="date" x-model="absensiFilter.startDate" @change="absensiPreset = 'custom'; loadAbsensiPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai</label>
                            <input type="date" x-model="absensiFilter.endDate" @change="absensiPreset = 'custom'; loadAbsensiPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Divisi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Divisi</label>
                            <select x-model="absensiFilter.divisionId" @change="loadAbsensiPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Divisi</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Karyawan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Karyawan</label>
                            <select x-model="absensiFilter.userId" @change="loadAbsensiPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Karyawan</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->username }} ({{ $emp->division->name ?? 'Tanpa Divisi' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Kehadiran -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status Masuk</label>
                            <select x-model="absensiFilter.status" @change="loadAbsensiPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="present">Hadir Tepat Waktu</option>
                                <option value="late">Hadir Terlambat</option>
                                <option value="not_clocked_out">Belum Absen Pulang</option>
                                <option value="clocked_out">Sudah Absen Pulang</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action and Export Bar -->
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="resetAbsensiFilter()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/60 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </button>
                            <span class="text-xs text-gray-400">|</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Total Data Cocok: <strong class="text-blue-600 dark:text-blue-400 font-bold" x-text="absensiStats.total">0</strong> catatan
                            </span>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Excel -->
                            <a :href="getAbsensiExportUrl('excel')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-sm transition duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                    <path d="M8.8 17l1.7-2.7L12.2 17h1.9l-2.6-3.8 2.5-3.7h-1.9l-1.6 2.6-1.6-2.6H7l2.5 3.7L6.9 17h1.9z"/>
                                </svg>
                                Unduh Excel (.xlsx)
                            </a>

                            <!-- PDF -->
                            <a :href="getAbsensiExportUrl('pdf')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-sm transition duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
                                </svg>
                                Unduh PDF (.pdf)
                            </a>

                            <!-- Print -->
                            <a :href="getAbsensiExportUrl('print')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 transition duration-150">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Langsung
                            </a>
                        </div>
                    </div>
                </div>

                <!-- KPI Metric Cards for Attendance -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Presensi</span>
                            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white mt-2" x-text="absensiStats.total">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Sesuai rentang & filter saat ini</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Tepat Waktu</span>
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2" x-text="absensiStats.present">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Presensi hadir tepat waktu</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Terlambat</span>
                            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-2" x-text="absensiStats.late">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Masuk melewati batas shift</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Belum Pulang</span>
                            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-2" x-text="absensiStats.not_clocked_out">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Belum melakukan presensi pulang</p>
                    </div>
                </div>

                <!-- Live Preview Table -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-ping"></div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Pratinjau Data Export (10 Baris Pertama)</h4>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            Menampilkan <span x-text="absensiItems.length">0</span> dari <span x-text="absensiStats.total">0</span> data
                        </span>
                    </div>

                    <!-- Loading State -->
                    <div x-show="isLoadingAbsensi" class="p-12 text-center">
                        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 font-semibold">Memuat pratinjau data presensi...</p>
                    </div>

                    <!-- Table -->
                    <div x-show="!isLoadingAbsensi" class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                            <thead class="text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3.5 text-center">No</th>
                                    <th scope="col" class="px-4 py-3.5">Tanggal</th>
                                    <th scope="col" class="px-4 py-3.5">Nama Karyawan</th>
                                    <th scope="col" class="px-4 py-3.5">Divisi</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Shift</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Jam Masuk</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Jam Pulang</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Durasi</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                <template x-for="item in absensiItems" :key="item.no">
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white" x-text="item.no"></td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap" x-text="item.raw_date"></td>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap" x-text="item.employee_name"></td>
                                        <td class="px-4 py-3 whitespace-nowrap" x-text="item.division"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.shift"></td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white whitespace-nowrap" x-text="item.time_in"></td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white whitespace-nowrap" x-text="item.time_out"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.duration"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span :class="item.status === 'late' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'" class="px-2 py-0.5 rounded-full text-[10px] font-bold">
                                                <span x-text="item.status_label"></span>
                                            </span>
                                            <template x-if="!item.is_clocked_out">
                                                <span class="ms-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                                    Belum Pulang
                                                </span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="absensiItems.length === 0">
                                    <tr>
                                        <td colspan="9" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Tidak ada data presensi yang sesuai dengan kriteria filter saat ini.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 2: LAPORAN PENGAJUAN IZIN & CUTI -->
            <!-- ============================================================== -->
            <div x-show="activeTab === 'izin'" x-cloak class="space-y-6">
                <!-- Filter Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 sm:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-100 dark:border-gray-700/60">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Filter Kriteria Pengajuan Izin & Cuti
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Filter data izin, sakit, dan cuti untuk pelaporan HRD.</p>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 me-1">Pintasan:</span>
                            <button type="button" @click="setIzinPreset('today')" :class="izinPreset === 'today' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Hari Ini</button>
                            <button type="button" @click="setIzinPreset('last7')" :class="izinPreset === 'last7' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">7 Hari</button>
                            <button type="button" @click="setIzinPreset('thisMonth')" :class="izinPreset === 'thisMonth' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Bulan Ini</button>
                            <button type="button" @click="setIzinPreset('lastMonth')" :class="izinPreset === 'lastMonth' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Bulan Lalu</button>
                            <button type="button" @click="setIzinPreset('thisYear')" :class="izinPreset === 'thisYear' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 font-semibold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200'" class="px-2.5 py-1 text-xs rounded-lg transition">Tahun Ini</button>
                        </div>
                    </div>

                    <!-- Filter Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai</label>
                            <input type="date" x-model="izinFilter.startDate" @change="izinPreset = 'custom'; loadIzinPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai</label>
                            <input type="date" x-model="izinFilter.endDate" @change="izinPreset = 'custom'; loadIzinPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Divisi</label>
                            <select x-model="izinFilter.divisionId" @change="loadIzinPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Semua Divisi</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Jenis Izin</label>
                            <select x-model="izinFilter.type" @change="loadIzinPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Semua Jenis</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="cuti">Cuti</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status Persetujuan</label>
                            <select x-model="izinFilter.status" @change="loadIzinPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Semua Status</option>
                                <option value="approved">Disetujui</option>
                                <option value="pending">Menunggu Persetujuan</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action and Export Bar -->
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="resetIzinFilter()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/60 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </button>
                            <span class="text-xs text-gray-400">|</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Total Pengajuan: <strong class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="izinStats.total">0</strong> pengajuan
                            </span>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex flex-wrap items-center gap-2">
                            <a :href="getIzinExportUrl('excel')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-sm transition duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                    <path d="M8.8 17l1.7-2.7L12.2 17h1.9l-2.6-3.8 2.5-3.7h-1.9l-1.6 2.6-1.6-2.6H7l2.5 3.7L6.9 17h1.9z"/>
                                </svg>
                                Unduh Excel (.xlsx)
                            </a>

                            <a :href="getIzinExportUrl('pdf')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-sm transition duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
                                </svg>
                                Unduh PDF (.pdf)
                            </a>

                            <a :href="getIzinExportUrl('print')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 transition duration-150">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Langsung
                            </a>
                        </div>
                    </div>
                </div>

                <!-- KPI Metric Cards for Leaves -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Pengajuan</span>
                            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white mt-2" x-text="izinStats.total">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Total seluruh permohonan</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Disetujui</span>
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2" x-text="izinStats.approved">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Telah disetujui admin</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Menunggu</span>
                            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-2" x-text="izinStats.pending">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Perlu tindakan verifikasi</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Ditolak</span>
                            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-2" x-text="izinStats.rejected">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Permohonan tidak disetujui</p>
                    </div>
                </div>

                <!-- Live Preview Table for Leaves -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Pratinjau Data Pengajuan (10 Baris Pertama)</h4>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            Menampilkan <span x-text="izinItems.length">0</span> dari <span x-text="izinStats.total">0</span> data
                        </span>
                    </div>

                    <div x-show="isLoadingIzin" class="p-12 text-center">
                        <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 font-semibold">Memuat pratinjau data perizinan...</p>
                    </div>

                    <div x-show="!isLoadingIzin" class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                            <thead class="text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3.5 text-center">No</th>
                                    <th scope="col" class="px-4 py-3.5">Tgl Pengajuan</th>
                                    <th scope="col" class="px-4 py-3.5">Nama Karyawan</th>
                                    <th scope="col" class="px-4 py-3.5">Divisi</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Jenis</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Rentang Tanggal</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Durasi</th>
                                    <th scope="col" class="px-4 py-3.5">Alasan</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                <template x-for="item in izinItems" :key="item.no">
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white" x-text="item.no"></td>
                                        <td class="px-4 py-3 whitespace-nowrap" x-text="item.created_at"></td>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap" x-text="item.employee_name"></td>
                                        <td class="px-4 py-3 whitespace-nowrap" x-text="item.division"></td>
                                        <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white whitespace-nowrap" x-text="item.type"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.date_range"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.duration"></td>
                                        <td class="px-4 py-3 max-w-xs truncate" :title="item.reason" x-text="item.reason"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span :class="{
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300': item.status === 'approved',
                                                'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300': item.status === 'rejected',
                                                'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': item.status === 'pending'
                                            }" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold">
                                                <span x-text="item.status_label"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="izinItems.length === 0">
                                    <tr>
                                        <td colspan="9" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Tidak ada data pengajuan izin pada rentang dan filter saat ini.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: REKAP BULANAN KARYAWAN -->
            <!-- ============================================================== -->
            <div x-show="activeTab === 'rekap'" x-cloak class="space-y-6">
                <!-- Filter Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6 sm:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-100 dark:border-gray-700/60">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                Periode Rekapitulasi Bulanan
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ringkasan total kehadiran, izin, sakit, dan cuti seluruh karyawan per bulan.</p>
                        </div>
                    </div>

                    <!-- Filter Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5">
                        <!-- Bulan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Bulan</label>
                            <select x-model="rekapFilter.month" @change="loadRekapPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                @php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    $currentMonth = \Carbon\Carbon::now()->month;
                                @endphp
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pilih Tahun</label>
                            <select x-model="rekapFilter.year" @change="loadRekapPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                @php
                                    $currentYear = \Carbon\Carbon::now()->year;
                                @endphp
                                @for ($y = $currentYear; $y >= $currentYear - 3; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Divisi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Divisi</label>
                            <select x-model="rekapFilter.divisionId" @change="loadRekapPreview()" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Divisi</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Action and Export Bar -->
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Periode Rekap: <strong class="text-indigo-600 dark:text-indigo-400 font-bold" x-text="rekapMonthLabel + ' ' + rekapFilter.year">Bulan</strong>
                            </span>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex flex-wrap items-center gap-2">
                            <a :href="getRekapExportUrl('excel')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-sm transition duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                                    <path d="M8.8 17l1.7-2.7L12.2 17h1.9l-2.6-3.8 2.5-3.7h-1.9l-1.6 2.6-1.6-2.6H7l2.5 3.7L6.9 17h1.9z"/>
                                </svg>
                                Unduh Rekap Excel (.xlsx)
                            </a>

                            <a :href="getRekapExportUrl('pdf')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-sm transition duration-150">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
                                </svg>
                                Unduh Rekap PDF (.pdf)
                            </a>

                            <a :href="getRekapExportUrl('print')" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 transition duration-150">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Langsung
                            </a>
                        </div>
                    </div>
                </div>

                <!-- KPI Metric Cards for Monthly Rekap -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Total Hadir</span>
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-2" x-text="rekapSummary.total_attendances">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Total seluruh kehadiran bulan ini</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Tepat Waktu</span>
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2" x-text="rekapSummary.total_present">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Presensi tepat waktu</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Terlambat</span>
                            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-2" x-text="rekapSummary.total_late">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Total keterlambatan akumulatif</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Izin / Sakit / Cuti</span>
                            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-2" x-text="(rekapSummary.total_izin + rekapSummary.total_sakit + rekapSummary.total_cuti)">0</div>
                        <p class="text-[11px] text-gray-400 mt-1">Izin: <span x-text="rekapSummary.total_izin">0</span> | Sakit: <span x-text="rekapSummary.total_sakit">0</span> | Cuti: <span x-text="rekapSummary.total_cuti">0</span></p>
                    </div>
                </div>

                <!-- Live Preview Table for Rekap -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-ping"></div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Tabel Ringkasan Per Karyawan (<span x-text="rekapMonthLabel + ' ' + rekapFilter.year"></span>)</h4>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            Total Karyawan: <span x-text="rekapItems.length">0</span>
                        </span>
                    </div>

                    <div x-show="isLoadingRekap" class="p-12 text-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 font-semibold">Menghitung rekapitulasi data kehadiran...</p>
                    </div>

                    <div x-show="!isLoadingRekap" class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-500 dark:text-gray-400">
                            <thead class="text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3.5 text-center">No</th>
                                    <th scope="col" class="px-4 py-3.5">Nama Karyawan</th>
                                    <th scope="col" class="px-4 py-3.5">Divisi</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">No. HP</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Tepat Waktu</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Terlambat</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Belum Pulang</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Izin</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Sakit</th>
                                    <th scope="col" class="px-4 py-3.5 text-center">Cuti</th>
                                    <th scope="col" class="px-4 py-3.5 text-center font-bold text-gray-900 dark:text-white">Total Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                <template x-for="(item, idx) in rekapItems" :key="item.user_id">
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white" x-text="idx + 1"></td>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap" x-text="item.username"></td>
                                        <td class="px-4 py-3 whitespace-nowrap" x-text="item.division"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.no_hp"></td>
                                        <td class="px-4 py-3 text-center font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap" x-text="item.present_count"></td>
                                        <td class="px-4 py-3 text-center font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap" x-text="item.late_count"></td>
                                        <td class="px-4 py-3 text-center text-amber-600 dark:text-amber-400 whitespace-nowrap" x-text="item.not_clocked_out_count"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.izin_count"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.sakit_count"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap" x-text="item.cuti_count"></td>
                                        <td class="px-4 py-3 text-center font-black text-gray-900 dark:text-white bg-gray-50/50 dark:bg-gray-700/30 whitespace-nowrap" x-text="item.total_attendances"></td>
                                    </tr>
                                </template>

                                <template x-if="rekapItems.length === 0">
                                    <tr>
                                        <td colspan="11" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                            Tidak ada data karyawan pada periode ini.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function exportCenter() {
            return {
                activeTab: 'absensi',
                absensiPreset: 'thisMonth',
                izinPreset: 'thisMonth',

                // Absensi Filter State
                absensiFilter: {
                    startDate: '{{ $defaultStartDate }}',
                    endDate: '{{ $defaultEndDate }}',
                    divisionId: '',
                    userId: '',
                    status: ''
                },
                absensiStats: @json($attendanceStats),
                absensiItems: [],
                isLoadingAbsensi: false,

                // Izin Filter State
                izinFilter: {
                    startDate: '{{ $defaultStartDate }}',
                    endDate: '{{ $defaultEndDate }}',
                    divisionId: '',
                    userId: '',
                    type: '',
                    status: ''
                },
                izinStats: @json($leaveStats),
                izinItems: [],
                isLoadingIzin: false,

                // Rekap Bulanan Filter State
                rekapFilter: {
                    month: '{{ \Carbon\Carbon::now()->month }}',
                    year: '{{ \Carbon\Carbon::now()->year }}',
                    divisionId: ''
                },
                rekapMonthLabel: '{{ \Carbon\Carbon::now()->locale("id")->isoFormat("MMMM") }}',
                rekapSummary: {
                    total_present: 0,
                    total_late: 0,
                    total_not_clocked_out: 0,
                    total_izin: 0,
                    total_sakit: 0,
                    total_cuti: 0,
                    total_attendances: 0
                },
                rekapItems: [],
                isLoadingRekap: false,

                init() {
                    this.loadAbsensiPreview();
                    this.loadIzinPreview();
                    this.loadRekapPreview();
                },

                setTab(tab) {
                    this.activeTab = tab;
                },

                // Preset logic for Absensi
                setAbsensiPreset(type) {
                    this.absensiPreset = type;
                    const today = new Date();
                    const formatDate = (d) => {
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    };

                    if (type === 'today') {
                        this.absensiFilter.startDate = formatDate(today);
                        this.absensiFilter.endDate = formatDate(today);
                    } else if (type === 'yesterday') {
                        const yest = new Date();
                        yest.setDate(today.getDate() - 1);
                        this.absensiFilter.startDate = formatDate(yest);
                        this.absensiFilter.endDate = formatDate(yest);
                    } else if (type === 'last7') {
                        const past = new Date();
                        past.setDate(today.getDate() - 6);
                        this.absensiFilter.startDate = formatDate(past);
                        this.absensiFilter.endDate = formatDate(today);
                    } else if (type === 'thisMonth') {
                        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                        this.absensiFilter.startDate = formatDate(firstDay);
                        this.absensiFilter.endDate = formatDate(today);
                    } else if (type === 'lastMonth') {
                        const firstDayPrev = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        const lastDayPrev = new Date(today.getFullYear(), today.getMonth(), 0);
                        this.absensiFilter.startDate = formatDate(firstDayPrev);
                        this.absensiFilter.endDate = formatDate(lastDayPrev);
                    }

                    this.loadAbsensiPreview();
                },

                resetAbsensiFilter() {
                    this.absensiFilter.divisionId = '';
                    this.absensiFilter.userId = '';
                    this.absensiFilter.status = '';
                    this.setAbsensiPreset('thisMonth');
                },

                loadAbsensiPreview() {
                    this.isLoadingAbsensi = true;
                    const params = new URLSearchParams(this.absensiFilter);
                    fetch(`{{ route('admin.export.absensi.preview') }}?${params.toString()}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.absensiStats = data.stats;
                                this.absensiItems = data.items;
                            }
                        })
                        .catch(err => {
                            console.error('Gagal memuat pratinjau absensi:', err);
                        })
                        .finally(() => {
                            this.isLoadingAbsensi = false;
                        });
                },

                getAbsensiExportUrl(format) {
                    const params = new URLSearchParams(this.absensiFilter);
                    if (format === 'excel') {
                        return `{{ route('admin.export.absensi.excel') }}?${params.toString()}`;
                    } else if (format === 'pdf') {
                        return `{{ route('admin.export.absensi.pdf') }}?${params.toString()}`;
                    } else if (format === 'print') {
                        return `{{ route('admin.export.absensi.print') }}?${params.toString()}`;
                    }
                },

                // Preset logic for Izin
                setIzinPreset(type) {
                    this.izinPreset = type;
                    const today = new Date();
                    const formatDate = (d) => {
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    };

                    if (type === 'today') {
                        this.izinFilter.startDate = formatDate(today);
                        this.izinFilter.endDate = formatDate(today);
                    } else if (type === 'last7') {
                        const past = new Date();
                        past.setDate(today.getDate() - 6);
                        this.izinFilter.startDate = formatDate(past);
                        this.izinFilter.endDate = formatDate(today);
                    } else if (type === 'thisMonth') {
                        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                        this.izinFilter.startDate = formatDate(firstDay);
                        this.izinFilter.endDate = formatDate(today);
                    } else if (type === 'lastMonth') {
                        const firstDayPrev = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        const lastDayPrev = new Date(today.getFullYear(), today.getMonth(), 0);
                        this.izinFilter.startDate = formatDate(firstDayPrev);
                        this.izinFilter.endDate = formatDate(lastDayPrev);
                    } else if (type === 'thisYear') {
                        const firstDayYear = new Date(today.getFullYear(), 0, 1);
                        this.izinFilter.startDate = formatDate(firstDayYear);
                        this.izinFilter.endDate = formatDate(today);
                    }

                    this.loadIzinPreview();
                },

                resetIzinFilter() {
                    this.izinFilter.divisionId = '';
                    this.izinFilter.userId = '';
                    this.izinFilter.type = '';
                    this.izinFilter.status = '';
                    this.setIzinPreset('thisMonth');
                },

                loadIzinPreview() {
                    this.isLoadingIzin = true;
                    const params = new URLSearchParams(this.izinFilter);
                    fetch(`{{ route('admin.export.izin.preview') }}?${params.toString()}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.izinStats = data.stats;
                                this.izinItems = data.items;
                            }
                        })
                        .catch(err => {
                            console.error('Gagal memuat pratinjau izin:', err);
                        })
                        .finally(() => {
                            this.isLoadingIzin = false;
                        });
                },

                getIzinExportUrl(format) {
                    const params = new URLSearchParams(this.izinFilter);
                    if (format === 'excel') {
                        return `{{ route('admin.export.izin.excel') }}?${params.toString()}`;
                    } else if (format === 'pdf') {
                        return `{{ route('admin.export.izin.pdf') }}?${params.toString()}`;
                    } else if (format === 'print') {
                        return `{{ route('admin.export.izin.print') }}?${params.toString()}`;
                    }
                },

                // Rekap Bulanan methods
                loadRekapPreview() {
                    this.isLoadingRekap = true;
                    const params = new URLSearchParams(this.rekapFilter);
                    fetch(`{{ route('admin.export.rekap.preview') }}?${params.toString()}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.rekapMonthLabel = data.month_label;
                                this.rekapSummary = data.stats;
                                this.rekapItems = data.items;
                            }
                        })
                        .catch(err => {
                            console.error('Gagal memuat pratinjau rekap:', err);
                        })
                        .finally(() => {
                            this.isLoadingRekap = false;
                        });
                },

                getRekapExportUrl(format) {
                    const params = new URLSearchParams(this.rekapFilter);
                    if (format === 'excel') {
                        return `{{ route('admin.export.rekap.excel') }}?${params.toString()}`;
                    } else if (format === 'pdf') {
                        return `{{ route('admin.export.rekap.pdf') }}?${params.toString()}`;
                    } else if (format === 'print') {
                        return `{{ route('admin.export.rekap.print') }}?${params.toString()}`;
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
