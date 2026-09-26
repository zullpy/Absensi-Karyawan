<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Master Data - Divisi') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Kelola daftar divisi kerja dan struktur departemen perusahaan.
                </p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-division-modal'))"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 active:bg-blue-900 transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Divisi</span>
            </button>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8" 
        x-data="{
            modalOpen: false,
            isEdit: false,
            formUrl: '{{ route('admin.master.divisi.store') }}',
            formData: { name: '', description: '' },
            openAdd() {
                this.isEdit = false;
                this.formUrl = '{{ route('admin.master.divisi.store') }}';
                this.formData = { name: '', description: '' };
                this.modalOpen = true;
            },
            openEdit(id, name, description) {
                this.isEdit = true;
                this.formUrl = '{{ url('admin/master/divisi') }}/' + id;
                this.formData = { name: name, description: description || '' };
                this.modalOpen = true;
            },
            confirmDelete(url, name) {
                Swal.fire({
                    title: 'Hapus Divisi?',
                    text: `Apakah Anda yakin ingin menghapus divisi '${name}'?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteDivisionForm');
                        form.action = url;
                        form.submit();
                    }
                });
            }
        }"
        @open-division-modal.window="openAdd()">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Alerts -->
            @if (session('success'))
                <div class="p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div><span class="font-bold">Berhasil:</span> {{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs">
                    <div class="font-bold mb-1">Gagal menyimpan data:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Table Container -->
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                
                <!-- Search Toolbar -->
                <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                    <form method="GET" action="{{ route('admin.master.divisi') }}" class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama divisi..."
                                class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg pl-8 pr-3 py-1.5 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 text-xs font-semibold transition cursor-pointer">
                            Cari
                        </button>
                        @if ($search)
                            <a href="{{ route('admin.master.divisi') }}" class="text-xs text-red-600 dark:text-red-400 hover:underline">Reset</a>
                        @endif
                    </form>
                </div>

                @if ($divisions->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                            <thead class="font-bold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-[#0f172a]/60 border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-5 py-3 w-16">No</th>
                                    <th class="px-5 py-3">Nama Divisi</th>
                                    <th class="px-5 py-3">Deskripsi</th>
                                    <th class="px-5 py-3">Jumlah Anggota</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/60 font-medium">
                                @foreach ($divisions as $index => $divisi)
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3.5 whitespace-nowrap text-gray-400">
                                            {{ $divisions->firstItem() + $index }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                            {{ $divisi->name }}
                                        </td>
                                        <td class="px-5 py-3.5 max-w-sm text-gray-600 dark:text-gray-300 truncate">
                                            {{ $divisi->description ?? '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                {{ $divisi->users_count }} Karyawan
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-right space-x-2">
                                            <button type="button" @click="openEdit({{ $divisi->id }}, @js($divisi->name), @js($divisi->description))" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">
                                                Edit
                                            </button>
                                            <button type="button" @click="confirmDelete('{{ route('admin.master.divisi.destroy', $divisi->id) }}', @js($divisi->name))" class="text-red-600 dark:text-red-400 font-semibold hover:underline cursor-pointer">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile List -->
                    <div class="block md:hidden divide-y divide-gray-200 dark:divide-slate-700 p-4 space-y-3.5">
                        @foreach ($divisions as $divisi)
                            <div class="pt-3.5 first:pt-0 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $divisi->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $divisi->users_count }} Karyawan
                                    </span>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $divisi->description ?? 'Tidak ada deskripsi' }}
                                </p>
                                <div class="flex items-center justify-end gap-3 pt-1">
                                    <button type="button" @click="openEdit({{ $divisi->id }}, @js($divisi->name), @js($divisi->description))" class="text-blue-600 dark:text-blue-400 font-semibold underline cursor-pointer">
                                        Edit
                                    </button>
                                    <button type="button" @click="confirmDelete('{{ route('admin.master.divisi.destroy', $divisi->id) }}', @js($divisi->name))" class="text-red-600 dark:text-red-400 font-semibold underline cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($divisions->hasPages())
                        <div class="p-3.5 border-t border-gray-200 dark:border-slate-700">
                            {{ $divisions->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-10 text-center text-gray-400 dark:text-gray-500 text-xs">
                        Tidak ada data divisi yang ditemukan.
                    </div>
                @endif

            </div>

        </div>

        <!-- Modal Tambah / Edit Divisi -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75" style="display: none;">
            <div class="bg-white dark:bg-[#1e293b] rounded-xl max-w-md w-full overflow-hidden shadow-xl border border-gray-200 dark:border-slate-700" @click.outside="modalOpen = false">
                <div class="p-4 flex items-center justify-between border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white" x-text="isEdit ? 'Edit Divisi' : 'Tambah Divisi Baru'"></h3>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="formUrl" method="POST" class="p-4 space-y-3.5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="!isEdit">

                    <div>
                        <label for="division_name" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                            Nama Divisi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="division_name" name="name" x-model="formData.name" required
                            placeholder="Contoh: IT Development, Keuangan, HRD"
                            class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label for="division_desc" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                            Deskripsi (Opsional)
                        </label>
                        <textarea id="division_desc" name="description" x-model="formData.description" rows="3"
                            placeholder="Keterangan tugas dan fungsi divisi..."
                            class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-200 dark:border-slate-700">
                        <button type="button" @click="modalOpen = false" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 transition cursor-pointer" x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Divisi'">
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hidden Delete Form -->
        <form id="deleteDivisionForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>
</x-app-layout>
