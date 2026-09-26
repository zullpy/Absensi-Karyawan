<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Riwayat Presensi Karyawan') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Daftar log kehadiran, jam masuk & keluar, foto selfie, dan koordinat presensi.
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                <span>Halaman Presensi</span>
            </a>
        </div>
    </x-slot>

    @php
        $indonesianMonths = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    @endphp

    <div class="py-6 sm:py-8" x-data="{ photoModal: false, modalPhotoUrl: '', modalTitle: '' }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-2 gap-3.5">
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Hadir Tepat Waktu</span>
                        <div class="text-xl font-bold text-emerald-700 dark:text-emerald-400">{{ $stats['total_present'] }} Hari</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Terlambat</span>
                        <div class="text-xl font-bold text-amber-700 dark:text-amber-400">{{ $stats['total_late'] }} Hari</div>
                    </div>
                </div>
            </div>

            <!-- Table & Filter Container -->
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                
                <!-- Filter Form -->
                <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                    <form method="GET" action="{{ route('user.attendance.history') }}" class="flex flex-wrap items-center gap-2">
                        <!-- Select Month (Bahasa Indonesia) -->
                        <select name="month" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-200">
                            @foreach ($indonesianMonths as $num => $name)
                                <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Select Year -->
                        <select name="year" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-200">
                            @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>

                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-700 text-white text-xs font-semibold hover:bg-blue-800 transition">
                            Tampilkan
                        </button>
                    </form>
                </div>

                @if ($attendances->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                            <thead class="font-bold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-[#0f172a]/60 border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-5 py-3">Hari & Tanggal</th>
                                    <th class="px-5 py-3">Jam Masuk</th>
                                    <th class="px-5 py-3">Jam Keluar</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Foto Selfie</th>
                                    <th class="px-5 py-3">Koordinat GPS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/60 font-medium">
                                @foreach ($attendances as $att)
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3.5 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d F Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i:s') : '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i:s') : '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($att->status === 'late')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                                    Terlambat
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                                    Tepat Waktu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                @if ($att->photo_in)
                                                    <button type="button" @click="modalPhotoUrl = '{{ $att->photo_in_url }}'; modalTitle = 'Foto Selfie Masuk ({{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }})'; photoModal = true" class="w-7 h-7 rounded border border-gray-300 dark:border-slate-600 overflow-hidden hover:opacity-80 transition" title="Foto Masuk">
                                                        <img src="{{ $att->photo_in_url }}" class="w-full h-full object-cover">
                                                    </button>
                                                @endif
                                                @if ($att->photo_out)
                                                    <button type="button" @click="modalPhotoUrl = '{{ $att->photo_out_url }}'; modalTitle = 'Foto Selfie Keluar ({{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }})'; photoModal = true" class="w-7 h-7 rounded border border-gray-300 dark:border-slate-600 overflow-hidden hover:opacity-80 transition" title="Foto Keluar">
                                                        <img src="{{ $att->photo_out_url }}" class="w-full h-full object-cover">
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($att->lat_in)
                                                <a href="https://www.google.com/maps?q={{ $att->lat_in }},{{ $att->long_in }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ round($att->lat_in, 4) }}, {{ round($att->long_in, 4) }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="block md:hidden divide-y divide-gray-200 dark:divide-slate-700 p-4 space-y-3.5">
                        @foreach ($attendances as $att)
                            <div class="pt-3.5 first:pt-0 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d F Y') }}
                                    </span>
                                    @if ($att->status === 'late')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">Terlambat</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">Tepat Waktu</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-gray-600 dark:text-gray-300">
                                    <div>Masuk: <span class="font-bold text-gray-800 dark:text-gray-100">{{ $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '-' }}</span></div>
                                    <div>Keluar: <span class="font-bold text-gray-800 dark:text-gray-100">{{ $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '-' }}</span></div>
                                </div>
                                <div class="flex items-center justify-end pt-1">
                                    @if ($att->photo_in)
                                        <button type="button" @click="modalPhotoUrl = '{{ $att->photo_in_url }}'; modalTitle = 'Foto Selfie Masuk'; photoModal = true" class="text-blue-600 dark:text-blue-400 font-semibold underline">
                                            Lihat Foto
                                        </button>
                                    @endif
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
                    <div class="p-10 text-center text-gray-400 dark:text-gray-500 text-xs">
                        Belum ada data presensi pada bulan ini.
                    </div>
                @endif

            </div>

        </div>

        <!-- Photo Modal -->
        <div x-show="photoModal" @click.outside="photoModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75" style="display: none;">
            <div class="bg-white dark:bg-[#1e293b] rounded-xl max-w-sm w-full overflow-hidden shadow-xl border border-gray-200 dark:border-slate-700" @click.stop>
                <div class="p-3.5 flex items-center justify-between border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
                    <button type="button" @click="photoModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-3 flex items-center justify-center bg-black">
                    <img :src="modalPhotoUrl" alt="Foto Selfie" class="rounded max-h-72 w-auto object-contain">
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
