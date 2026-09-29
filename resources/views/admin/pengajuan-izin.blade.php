<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Persetujuan & Data Pengajuan Izin') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Verifikasi, setujui, atau tolak permohonan izin, cuti, dan sakit dari karyawan.
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
            rejectModalOpen: false,
            photoModalOpen: false,
            photoModalUrl: '',
            photoModalTitle: '',
            selectedLeave: null,
            rejectActionUrl: '',
            rejectEmployeeName: '',
            rejectLeaveType: '',
            rejectReason: '',
            openDetail(data) {
                this.selectedLeave = data;
                this.detailModalOpen = true;
            },
            openReject(id, name, type) {
                this.rejectActionUrl = '{{ url('admin/pengajuan-izin') }}/' + id + '/reject';
                this.rejectEmployeeName = name;
                this.rejectLeaveType = type;
                this.rejectReason = '';
                this.rejectModalOpen = true;
                if (this.detailModalOpen) {
                    this.detailModalOpen = false;
                }
            },
            openPhoto(url, title) {
                if (!url) return;
                this.photoModalUrl = url;
                this.photoModalTitle = title;
                this.photoModalOpen = true;
            },
            confirmApprove(url, name, type) {
                const isDark = document.documentElement.classList.contains('dark');
                Swal.fire({
                    title: 'Setujui Permohonan?',
                    text: `Apakah Anda yakin ingin menyetujui pengajuan ${type} untuk ${name}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: isDark ? '#334155' : '#94a3b8',
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('approveLeaveForm');
                        form.action = url;
                        form.submit();
                    }
                });
            },
            confirmDelete(url, name, type) {
                const isDark = document.documentElement.classList.contains('dark');
                Swal.fire({
                    title: 'Hapus Pengajuan Izin?',
                    text: `Apakah Anda yakin ingin menghapus catatan pengajuan ${type} untuk ${name}? Tindakan ini tidak dapat dibatalkan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: isDark ? '#334155' : '#94a3b8',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteLeaveForm');
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

            @if (isset($errors) && $errors->any())
                <div class="p-3.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Terjadi kesalahan saat memproses:</span>
                    </div>
                    <ul class="list-disc list-inside ml-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- KPI Cards Overview -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Total Pengajuan -->
                <a href="{{ route('admin.pengajuan-izin') }}"
                   class="group bg-white dark:bg-[#1e293b] rounded-2xl p-4 border border-gray-200/80 dark:border-slate-700/80 hover:border-blue-400 dark:hover:border-blue-500/50 shadow-xs flex items-center justify-between transition cursor-pointer">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Total Pengajuan</span>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $totalCount }}</div>
                        <span class="text-[11px] text-gray-400">Semua permohonan</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 flex items-center justify-center group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </a>

                <!-- Menunggu Persetujuan -->
                <a href="{{ route('admin.pengajuan-izin', ['status' => 'pending']) }}"
                   class="group bg-white dark:bg-[#1e293b] rounded-2xl p-4 border border-gray-200/80 dark:border-slate-700/80 hover:border-amber-400 dark:hover:border-amber-500/50 shadow-xs flex items-center justify-between transition cursor-pointer">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Menunggu Konfirmasi</span>
                            @if ($pendingCount > 0)
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $pendingCount }}</div>
                        <span class="text-[11px] text-gray-400">Perlu tindakan admin</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300 flex items-center justify-center group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                <!-- Disetujui -->
                <a href="{{ route('admin.pengajuan-izin', ['status' => 'approved']) }}"
                   class="group bg-white dark:bg-[#1e293b] rounded-2xl p-4 border border-gray-200/80 dark:border-slate-700/80 hover:border-emerald-400 dark:hover:border-emerald-500/50 shadow-xs flex items-center justify-between transition cursor-pointer">
                    <div>
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Disetujui</span>
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $approvedCount }}</div>
                        <span class="text-[11px] text-gray-400">Permohonan diterima</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300 flex items-center justify-center group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                <!-- Ditolak -->
                <a href="{{ route('admin.pengajuan-izin', ['status' => 'rejected']) }}"
                   class="group bg-white dark:bg-[#1e293b] rounded-2xl p-4 border border-gray-200/80 dark:border-slate-700/80 hover:border-red-400 dark:hover:border-red-500/50 shadow-xs flex items-center justify-between transition cursor-pointer">
                    <div>
                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">Ditolak</span>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-0.5">{{ $rejectedCount }}</div>
                        <span class="text-[11px] text-gray-400">Permohonan ditolak</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300 flex items-center justify-center group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Table Container & Filter Card -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-gray-200/80 dark:border-slate-700/80 overflow-hidden">
                
                <!-- Status Filter Tabs (Top Bar) -->
                <div class="px-5 pt-4 pb-0 flex items-center justify-between gap-3 border-b border-gray-100 dark:border-slate-700/60 overflow-x-auto">
                    <div class="flex items-center gap-1.5 pb-3">
                        <!-- Tab Semua -->
                        <a href="{{ route('admin.pengajuan-izin', request()->except(['status', 'page'])) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ empty($status) ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                            <span>Semua</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ empty($status) ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300' }}">
                                {{ $totalCount }}
                            </span>
                        </a>

                        <!-- Tab Menunggu -->
                        <a href="{{ route('admin.pengajuan-izin', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ ($status ?? '') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                            <span>Menunggu</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ ($status ?? '') === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' }}">
                                {{ $pendingCount }}
                            </span>
                        </a>

                        <!-- Tab Disetujui -->
                        <a href="{{ route('admin.pengajuan-izin', array_merge(request()->except('page'), ['status' => 'approved'])) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ ($status ?? '') === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                            <span>Disetujui</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($status ?? '') === 'approved' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' }}">
                                {{ $approvedCount }}
                            </span>
                        </a>

                        <!-- Tab Ditolak -->
                        <a href="{{ route('admin.pengajuan-izin', array_merge(request()->except('page'), ['status' => 'rejected'])) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-2 {{ ($status ?? '') === 'rejected' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                            <span>Ditolak</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($status ?? '') === 'rejected' ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">
                                {{ $rejectedCount }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Secondary Filter Bar (Search, Dropdowns, Date Range) -->
                <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-slate-700/60 bg-transparent">
                    <form method="GET" action="{{ route('admin.pengajuan-izin') }}" class="flex flex-col xl:flex-row xl:items-center justify-between gap-3.5">
                        @if (!empty($status))
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif

                        <!-- Search Box (Left) -->
                        <div class="relative w-full xl:w-80">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Cari nama / no HP karyawan..."
                                style="padding-left: 2.5rem;"
                                class="w-full h-9 pl-10 pr-3.5 text-xs rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-[#0f172a] text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition outline-none">
                        </div>

                        <!-- Right Filters Group -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            
                            <!-- Jenis Izin Filter -->
                            <select name="type" onchange="this.form.submit()"
                                class="h-9 bg-gray-50/70 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 rounded-xl px-3 text-xs text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer outline-none">
                                <option value="">Semua Jenis Izin</option>
                                <option value="izin" {{ ($type ?? '') === 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ ($type ?? '') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="cuti" {{ ($type ?? '') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>

                            <!-- Divisi Filter -->
                            <select name="division_id" onchange="this.form.submit()"
                                class="h-9 bg-gray-50/70 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 rounded-xl px-3 text-xs text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer outline-none">
                                <option value="">Semua Divisi</option>
                                @foreach ($divisions as $div)
                                    <option value="{{ $div->id }}" {{ ($divisionId ?? '') == $div->id ? 'selected' : '' }}>
                                        {{ $div->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Clean Unified Date Range Picker -->
                            <div class="inline-flex items-center h-9 px-2.5 rounded-xl bg-gray-50/70 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 text-xs text-gray-700 dark:text-gray-200">
                                <svg class="w-3.5 h-3.5 text-gray-400 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" title="Tanggal Mulai"
                                    class="bg-transparent border-0 p-0 text-xs text-gray-800 dark:text-gray-200 focus:ring-0 cursor-pointer dark:[color-scheme:dark]">
                                <span class="text-gray-400 mx-1.5 font-medium">&ndash;</span>
                                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" title="Tanggal Selesai"
                                    class="bg-transparent border-0 p-0 text-xs text-gray-800 dark:text-gray-200 focus:ring-0 cursor-pointer dark:[color-scheme:dark]">
                            </div>

                            <!-- Terapkan Button -->
                            <button type="submit"
                                class="h-9 inline-flex items-center gap-1.5 px-3.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 transition shadow-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Filter</span>
                            </button>

                            <!-- Reset Button -->
                            @if ($search || $type || $divisionId || $startDate || $endDate)
                                <a href="{{ route('admin.pengajuan-izin', !empty($status) ? ['status' => $status] : []) }}"
                                    class="h-9 inline-flex items-center gap-1 px-3 rounded-xl text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                                    title="Reset filter">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Reset</span>
                                </a>
                            @endif

                        </div>
                    </form>
                </div>

                <!-- Content: Table (Desktop) -->
                @if ($leaves->count() > 0)
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-slate-700 bg-gray-50/75 dark:bg-[#0f172a]/50 text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider text-[11px]">
                                    <th class="px-5 py-3.5">Karyawan</th>
                                    <th class="px-5 py-3.5">Jenis</th>
                                    <th class="px-5 py-3.5">Alasan</th>
                                    <th class="px-5 py-3.5">Status</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/60 font-medium">
                                @foreach ($leaves as $item)
                                    @php
                                        $user = $item->user;
                                        $isImg = $item->is_image_attachment;
                                        $isPdf = $item->is_pdf_attachment;
                                        $attUrl = $item->attachment_url;
                                        $duration = $item->duration_days;
                                        $jsonPayload = json_encode([
                                            'id' => $item->id,
                                            'username' => $user->username ?? 'Unknown',
                                            'no_hp' => $user->no_hp ?? '-',
                                            'division' => $user->division->name ?? 'Belum Diatur',
                                            'photo' => $user->profile_photo_url ?? null,
                                            'type' => $item->type,
                                            'type_label' => ucfirst($item->type),
                                            'start_date' => \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M Y'),
                                            'end_date' => \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y'),
                                            'duration' => $duration . ' Hari',
                                            'reason' => $item->reason,
                                            'attachment' => $attUrl,
                                            'is_image' => $isImg,
                                            'is_pdf' => $isPdf,
                                            'status' => $item->status,
                                            'rejection_note' => $item->rejection_note,
                                            'created_at' => $item->created_at->translatedFormat('d M Y H:i'),
                                            'approve_url' => route('admin.pengajuan-izin.approve', $item->id),
                                            'delete_url' => route('admin.pengajuan-izin.destroy', $item->id),
                                        ]);
                                    @endphp
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/30 transition">
                                        
                                        <!-- 1. Karyawan -->
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $user?->profile_photo_url }}"
                                                    alt="{{ $user?->username ?? 'User' }}"
                                                    class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0">
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white leading-tight">
                                                        {{ $user?->username ?? 'Unknown User' }}
                                                    </div>
                                                    <div class="text-[11px] text-gray-400 mt-0.5">
                                                        {{ $user?->division->name ?? 'Belum ada divisi' }} &bull; {{ $user?->no_hp ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 2. Jenis Izin -->
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($item->type === 'sakit')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Sakit
                                                </span>
                                            @elseif ($item->type === 'cuti')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                    Cuti
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Izin
                                                </span>
                                            @endif
                                        </td>

                                        <!-- 3. Alasan / Keterangan -->
                                        <td class="px-5 py-3.5 max-w-sm">
                                            <p class="text-gray-600 dark:text-gray-300 line-clamp-2" title="{{ $item->reason }}">
                                                {{ $item->reason }}
                                            </p>
                                        </td>

                                        <!-- 4. Status Badge -->
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if ($item->status === 'approved')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    Disetujui
                                                </span>
                                            @elseif ($item->status === 'rejected')
                                                <div>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Ditolak
                                                    </span>
                                                    @if ($item->rejection_note)
                                                        <div class="text-[10px] text-red-600 dark:text-red-400 mt-0.5 max-w-[150px] truncate" title="Alasan: {{ $item->rejection_note }}">
                                                            {{ $item->rejection_note }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                    Menunggu
                                                </span>
                                            @endif
                                        </td>

                                        <!-- 5. Actions -->
                                        <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                
                                                <!-- Detail Button -->
                                                <button type="button"
                                                    @click='openDetail({{ $jsonPayload }})'
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition cursor-pointer text-xs font-semibold"
                                                    title="Lihat Detail Lengkap">
                                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span>Detail</span>
                                                </button>

                                                @if ($item->status === 'pending')
                                                    <!-- Quick Approve Button -->
                                                    <button type="button"
                                                        @click="confirmApprove('{{ route('admin.pengajuan-izin.approve', $item->id) }}', '{{ $user?->username }}', '{{ ucfirst($item->type) }}')"
                                                        class="p-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-xs cursor-pointer"
                                                        title="Setujui Izin">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>

                                                    <!-- Quick Reject Button -->
                                                    <button type="button"
                                                        @click="openReject('{{ $item->id }}', '{{ $user?->username }}', '{{ ucfirst($item->type) }}')"
                                                        class="p-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white transition shadow-xs cursor-pointer"
                                                        title="Tolak Izin">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                @endif

                                                <!-- Delete Button -->
                                                <button type="button"
                                                    @click="confirmDelete('{{ route('admin.pengajuan-izin.destroy', $item->id) }}', '{{ $user?->username }}', '{{ ucfirst($item->type) }}')"
                                                    class="p-1.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                                                    title="Hapus Catatan">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Content: Mobile Cards List -->
                    <div class="block md:hidden divide-y divide-gray-200 dark:divide-slate-700 p-4 space-y-4">
                        @foreach ($leaves as $item)
                            @php
                                $user = $item->user;
                                $isImg = $item->is_image_attachment;
                                $isPdf = $item->is_pdf_attachment;
                                $attUrl = $item->attachment_url;
                                $duration = $item->duration_days;
                                $jsonPayload = json_encode([
                                    'id' => $item->id,
                                    'username' => $user->username ?? 'Unknown',
                                    'no_hp' => $user->no_hp ?? '-',
                                    'division' => $user->division->name ?? 'Belum Diatur',
                                    'photo' => $user->profile_photo_url ?? null,
                                    'type' => $item->type,
                                    'type_label' => ucfirst($item->type),
                                    'start_date' => \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M Y'),
                                    'end_date' => \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y'),
                                    'duration' => $duration . ' Hari',
                                    'reason' => $item->reason,
                                    'attachment' => $attUrl,
                                    'is_image' => $isImg,
                                    'is_pdf' => $isPdf,
                                    'status' => $item->status,
                                    'rejection_note' => $item->rejection_note,
                                    'created_at' => $item->created_at->translatedFormat('d M Y H:i'),
                                    'approve_url' => route('admin.pengajuan-izin.approve', $item->id),
                                    'delete_url' => route('admin.pengajuan-izin.destroy', $item->id),
                                ]);
                            @endphp
                            <div class="pt-4 first:pt-0 space-y-2.5 text-xs">
                                
                                <!-- Top Row: Avatar & Status -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $user?->profile_photo_url }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $user?->username }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $user?->division->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        @if ($item->status === 'approved')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">Disetujui</span>
                                        @elseif ($item->status === 'rejected')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Ditolak</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">Menunggu</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Leave Details Banner -->
                                <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700 flex items-center justify-between">
                                    <div>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $item->type === 'sakit' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300' : ($item->type === 'cuti' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300') }}">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                        <div class="font-bold text-gray-800 dark:text-gray-200 mt-1">
                                            {{ \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y') }}
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                        {{ $duration }} Hari
                                    </span>
                                </div>

                                <!-- Reason snippet -->
                                <p class="text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ $item->reason }}
                                </p>

                                @if ($item->rejection_note)
                                    <div class="p-2 rounded bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-[11px] text-red-700 dark:text-red-300">
                                        <span class="font-bold">Alasan Penolakan:</span> {{ $item->rejection_note }}
                                    </div>
                                @endif

                                <!-- Actions row for Mobile -->
                                <div class="pt-2 flex items-center justify-between border-t border-gray-100 dark:border-slate-700/60">
                                    <button type="button" @click='openDetail({{ $jsonPayload }})' class="text-blue-600 dark:text-blue-400 font-semibold text-xs">
                                        Lihat Detail &rarr;
                                    </button>
                                    
                                    <div class="flex items-center gap-2">
                                        @if ($item->status === 'pending')
                                            <button type="button"
                                                @click="confirmApprove('{{ route('admin.pengajuan-izin.approve', $item->id) }}', '{{ $user?->username }}', '{{ ucfirst($item->type) }}')"
                                                class="px-2.5 py-1 rounded text-xs font-semibold bg-emerald-600 text-white">
                                                Setujui
                                            </button>
                                            <button type="button"
                                                @click="openReject('{{ $item->id }}', '{{ $user?->username }}', '{{ ucfirst($item->type) }}')"
                                                class="px-2.5 py-1 rounded text-xs font-semibold bg-red-600 text-white">
                                                Tolak
                                            </button>
                                        @endif
                                        <button type="button"
                                            @click="confirmDelete('{{ route('admin.pengajuan-izin.destroy', $item->id) }}', '{{ $user?->username }}', '{{ ucfirst($item->type) }}')"
                                            class="text-gray-400 hover:text-red-600 p-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($leaves->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-[#0f172a]/20">
                            {{ $leaves->links() }}
                        </div>
                    @endif

                @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Tidak ada pengajuan izin</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-sm mx-auto">
                            @if ($search || $status || $type || $divisionId || $startDate || $endDate)
                                Tidak ditemukan data pengajuan yang sesuai dengan kriteria filter saat ini.
                            @else
                                Belum ada riwayat pengajuan izin, cuti, atau sakit dari karyawan.
                            @endif
                        </p>
                        @if ($search || $status || $type || $divisionId || $startDate || $endDate)
                            <a href="{{ route('admin.pengajuan-izin') }}" class="inline-flex items-center gap-1 mt-3 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 transition">
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
                            <span>Detail Pengajuan Izin Karyawan</span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedLeave ? 'Diajukan pada ' + selectedLeave.created_at : ''"></p>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <template x-if="selectedLeave">
                    <div class="p-5 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                        
                        <!-- Employee Banner -->
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-[#0f172a] border border-gray-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <img :src="selectedLeave.photo" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700">
                                <div>
                                    <div class="font-bold text-sm text-gray-900 dark:text-white" x-text="selectedLeave.username"></div>
                                    <div class="text-gray-400 text-xs mt-0.5">
                                        <span x-text="'Divisi: ' + selectedLeave.division"></span> &bull; 
                                        <span x-text="'HP: ' + selectedLeave.no_hp"></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300': selectedLeave.status === 'approved',
                                        'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': selectedLeave.status === 'rejected',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300': selectedLeave.status === 'pending'
                                    }"
                                    x-text="selectedLeave.status === 'approved' ? 'Disetujui' : (selectedLeave.status === 'rejected' ? 'Ditolak' : 'Menunggu')">
                                </span>
                            </div>
                        </div>

                        <!-- Technical Details Grid -->
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 divide-y divide-gray-200 dark:divide-slate-700 overflow-hidden">
                            <div class="p-2.5 flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Jenis Izin</span>
                                <span class="font-bold text-gray-900 dark:text-white uppercase" x-text="selectedLeave.type_label"></span>
                            </div>
                            <div class="p-2.5 flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Rentang Tanggal</span>
                                <span class="font-bold text-gray-900 dark:text-white" x-text="selectedLeave.start_date + ' s/d ' + selectedLeave.end_date"></span>
                            </div>
                            <div class="p-2.5 flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Total Durasi</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400" x-text="selectedLeave.duration"></span>
                            </div>
                        </div>

                        <!-- Reason / Description Section -->
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-3.5 bg-white dark:bg-slate-800/40">
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1.5">Alasan / Penjelasan</span>
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line leading-relaxed" x-text="selectedLeave.reason"></p>
                        </div>

                        <!-- Rejection Note (if any) -->
                        <template x-if="selectedLeave.rejection_note">
                            <div class="rounded-xl border border-red-200 dark:border-red-900/60 p-3.5 bg-red-50/50 dark:bg-red-950/30">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400 mb-1">Catatan Penolakan Admin:</span>
                                <p class="text-red-800 dark:text-red-300 font-medium" x-text="selectedLeave.rejection_note"></p>
                            </div>
                        </template>

                        <!-- Attachment Preview Section -->
                        <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-3.5 bg-white dark:bg-slate-800/40">
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Lampiran Bukti / Surat Dokter</span>
                            <template x-if="selectedLeave.attachment && selectedLeave.is_image">
                                <div>
                                    <img :src="selectedLeave.attachment" class="w-full max-h-48 object-contain rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer hover:opacity-90 transition shadow-xs"
                                        @click="openPhoto(selectedLeave.attachment, 'Lampiran - ' + selectedLeave.username)">
                                    <div class="mt-2 text-center">
                                        <button type="button" @click="openPhoto(selectedLeave.attachment, 'Lampiran - ' + selectedLeave.username)" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                                            Perbesar Foto &rarr;
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="selectedLeave.attachment && selectedLeave.is_pdf">
                                <div class="p-4 rounded-lg bg-red-50/50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-7 h-7 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">Dokumen Surat / Bukti (PDF)</div>
                                            <div class="text-[11px] text-gray-500">Klik tombol untuk membaca dokumen</div>
                                        </div>
                                    </div>
                                    <a :href="selectedLeave.attachment" target="_blank"
                                        class="px-3 py-1.5 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                                        Buka PDF
                                    </a>
                                </div>
                            </template>
                            <template x-if="!selectedLeave.attachment">
                                <div class="p-4 rounded-lg bg-gray-50 dark:bg-[#0f172a] text-center text-gray-400">
                                    Tidak ada berkas lampiran yang dilampirkan oleh karyawan.
                                </div>
                            </template>
                        </div>

                    </div>
                </template>

                <!-- Modal Actions Footer -->
                <div class="p-3.5 bg-gray-50 dark:bg-[#0f172a] border-t border-gray-200 dark:border-slate-700 flex items-center justify-between">
                    <button type="button" @click="detailModalOpen = false"
                        class="px-4 py-1.5 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-600 transition cursor-pointer">
                        Tutup
                    </button>

                    <template x-if="selectedLeave && selectedLeave.status === 'pending'">
                        <div class="flex items-center gap-2">
                            <button type="button"
                                @click="openReject(selectedLeave.id, selectedLeave.username, selectedLeave.type_label)"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition shadow-xs cursor-pointer">
                                Tolak Izin
                            </button>
                            <button type="button"
                                @click="confirmApprove(selectedLeave.approve_url, selectedLeave.username, selectedLeave.type_label)"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                                Setujui Izin
                            </button>
                        </div>
                    </template>
                </div>

            </div>
        </div>

        <!-- Reject Modal Form (Alpine.js) -->
        <div x-show="rejectModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75"
            style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-gray-200 dark:border-slate-700"
                @click.outside="rejectModalOpen = false">
                
                <form :action="rejectActionUrl" method="POST">
                    @csrf
                    
                    <div class="p-4 sm:p-5 flex items-center justify-between border-b border-gray-200 dark:border-slate-700 bg-red-50/50 dark:bg-red-950/20">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-300 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Tolak Pengajuan Izin</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="rejectEmployeeName + ' (' + rejectLeaveType + ')'"></p>
                            </div>
                        </div>
                        <button type="button" @click="rejectModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer p-1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-5 space-y-3.5 text-xs">
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Silakan tuliskan alasan penolakan permohonan izin ini. Catatan ini akan dikirimkan kepada karyawan agar dapat dipahami.
                        </p>

                        <div>
                            <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_note" rows="3" required
                                placeholder="Contoh: Jadwal bentrok dengan agenda penting perusahaan, kuota cuti tahunan telah habis, dsb."
                                class="w-full text-xs rounded-xl border border-gray-300 dark:border-slate-600 bg-white dark:bg-[#0f172a] text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-red-500 focus:border-red-500 placeholder-gray-400 p-2.5"></textarea>
                        </div>
                    </div>

                    <div class="p-3.5 bg-gray-50 dark:bg-[#0f172a] border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-2">
                        <button type="button" @click="rejectModalOpen = false"
                            class="px-4 py-1.5 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm">
                            Tolak Pengajuan
                        </button>
                    </div>

                </form>

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
            
            <div class="max-w-xl w-full bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl border border-gray-200 dark:border-slate-700"
                @click.outside="photoModalOpen = false">
                
                <div class="p-3.5 flex items-center justify-between border-b border-gray-200 dark:border-slate-700">
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200" x-text="photoModalTitle"></h4>
                    <button type="button" @click="photoModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-3 bg-black/5 dark:bg-black/40 flex items-center justify-center">
                    <img :src="photoModalUrl" class="max-h-[70vh] w-auto rounded-lg object-contain shadow-md">
                </div>

                <div class="p-3 text-right">
                    <button type="button" @click="photoModalOpen = false" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-200 dark:bg-slate-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-slate-600 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden Forms for Approve & Delete Actions -->
        <form id="approveLeaveForm" method="POST" style="display: none;">
            @csrf
        </form>

        <form id="deleteLeaveForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    </div>
</x-app-layout>
