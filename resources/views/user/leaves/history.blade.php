<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Riwayat Pengajuan Izin') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Pantau status persetujuan surat izin, sakit, dan cuti kerja Anda.
                </p>
            </div>
            <a href="{{ route('user.leaves.create') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Ajukan Izin Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Success Alert -->
            @if (session('success'))
                <div class="p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div>
                        <span class="font-bold">Berhasil:</span> {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-3.5 border border-gray-200 dark:border-slate-700 shadow-sm">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Total Pengajuan</span>
                    <div class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-3.5 border border-gray-200 dark:border-slate-700 shadow-sm">
                    <span class="text-xs text-amber-600 dark:text-amber-400">Menunggu</span>
                    <div class="text-xl font-bold text-amber-700 dark:text-amber-400 mt-0.5">{{ $stats['pending'] }}</div>
                </div>
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-3.5 border border-gray-200 dark:border-slate-700 shadow-sm">
                    <span class="text-xs text-emerald-600 dark:text-emerald-400">Disetujui</span>
                    <div class="text-xl font-bold text-emerald-700 dark:text-emerald-400 mt-0.5">{{ $stats['approved'] }}</div>
                </div>
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-3.5 border border-gray-200 dark:border-slate-700 shadow-sm">
                    <span class="text-xs text-red-600 dark:text-red-400">Ditolak</span>
                    <div class="text-xl font-bold text-red-700 dark:text-red-400 mt-0.5">{{ $stats['rejected'] }}</div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                
                <!-- Filter Tabs -->
                <div class="p-3.5 border-b border-gray-200 dark:border-slate-700 flex items-center gap-1.5 overflow-x-auto">
                    <a href="{{ route('user.leaves.history') }}" class="px-3 py-1 rounded-md text-xs font-semibold transition {{ empty($status) ? 'bg-blue-700 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('user.leaves.history', ['status' => 'pending']) }}" class="px-3 py-1 rounded-md text-xs font-semibold transition {{ $status === 'pending' ? 'bg-amber-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                        Menunggu
                    </a>
                    <a href="{{ route('user.leaves.history', ['status' => 'approved']) }}" class="px-3 py-1 rounded-md text-xs font-semibold transition {{ $status === 'approved' ? 'bg-emerald-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                        Disetujui
                    </a>
                    <a href="{{ route('user.leaves.history', ['status' => 'rejected']) }}" class="px-3 py-1 rounded-md text-xs font-semibold transition {{ $status === 'rejected' ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                        Ditolak
                    </a>
                </div>

                @if ($leaves->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                            <thead class="font-bold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-[#0f172a]/60 border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-5 py-3">Jenis Izin</th>
                                    <th class="px-5 py-3">Rentang Tanggal</th>
                                    <th class="px-5 py-3">Keterangan / Alasan</th>
                                    <th class="px-5 py-3">Lampiran</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Tgl Pengajuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/60 font-medium">
                                @foreach ($leaves as $leave)
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $leave->type === 'sakit' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' : ($leave->type === 'izin' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300') }}">
                                                {{ ucfirst($leave->type) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <div class="font-bold text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="text-gray-400 text-[11px]">
                                                ({{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} Hari)
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 max-w-xs truncate text-gray-600 dark:text-gray-300">
                                            {{ $leave->reason }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($leave->attachment)
                                                <a href="{{ $leave->attachment_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline inline-flex items-center gap-1">
                                                    Lihat Berkas
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($leave->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                                    Disetujui
                                                </span>
                                            @elseif ($leave->status === 'rejected')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                                    Menunggu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-gray-400 text-[11px]">
                                            {{ $leave->created_at->translatedFormat('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile List -->
                    <div class="block md:hidden divide-y divide-gray-200 dark:divide-slate-700 p-4 space-y-3.5">
                        @foreach ($leaves as $leave)
                            <div class="pt-3.5 first:pt-0 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">
                                        {{ ucfirst($leave->type) }}
                                    </span>
                                    @if ($leave->status === 'approved')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">Disetujui</span>
                                    @elseif ($leave->status === 'rejected')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Ditolak</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">Menunggu</span>
                                    @endif
                                </div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ $leave->reason }}
                                </p>
                                @if ($leave->attachment)
                                    <div class="pt-1">
                                        <a href="{{ $leave->attachment_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 font-semibold underline">Lihat Lampiran</a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($leaves->hasPages())
                        <div class="p-3.5 border-t border-gray-200 dark:border-slate-700">
                            {{ $leaves->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-10 text-center text-gray-400 dark:text-gray-500 text-xs">
                        Belum ada pengajuan izin yang tercatat.
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
