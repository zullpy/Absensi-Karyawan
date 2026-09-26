<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Master Data - Data Admin') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Kelola akun administrator dengan hak akses penuh ke sistem.
                </p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-admin-modal'))"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 active:bg-blue-900 transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Admin</span>
            </button>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8"
        x-data="{
            modalOpen: false,
            isEdit: false,
            formUrl: '{{ route('admin.master.admin.store') }}',
            formData: { username: '', no_hp: '' },
            openAdd() {
                this.isEdit = false;
                this.formUrl = '{{ route('admin.master.admin.store') }}';
                this.formData = { username: '', no_hp: '' };
                this.modalOpen = true;
            },
            openEdit(id, username, noHp) {
                this.isEdit = true;
                this.formUrl = '{{ url('admin/master/admin') }}/' + id;
                this.formData = { username: username, no_hp: noHp };
                this.modalOpen = true;
            },
            confirmDelete(url, username) {
                Swal.fire({
                    title: 'Hapus Admin?',
                    text: `Apakah Anda yakin ingin menghapus administrator '${username}'?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteAdminForm');
                        form.action = url;
                        form.submit();
                    }
                });
            }
        }"
        @open-admin-modal.window="openAdd()">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Alerts -->
            @if (session('success'))
                <div class="p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div><span class="font-bold">Berhasil:</span> {{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-3.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    <div><span class="font-bold">Perhatian:</span> {{ session('error') }}</div>
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
                    <form method="GET" action="{{ route('admin.master.admin') }}" class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari username atau no HP..."
                                class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg pl-8 pr-3 py-1.5 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 text-xs font-semibold transition cursor-pointer">
                            Cari
                        </button>
                        @if ($search)
                            <a href="{{ route('admin.master.admin') }}" class="text-xs text-red-600 dark:text-red-400 hover:underline">Reset</a>
                        @endif
                    </form>
                </div>

                @if ($admins->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-700 dark:text-gray-300">
                            <thead class="font-bold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-[#0f172a]/60 border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-5 py-3 w-14">No</th>
                                    <th class="px-5 py-3">Username</th>
                                    <th class="px-5 py-3">No. HP (WhatsApp)</th>
                                    <th class="px-5 py-3">Role</th>
                                    <th class="px-5 py-3">Terdaftar</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700/60 font-medium">
                                @foreach ($admins as $index => $adm)
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3.5 whitespace-nowrap text-gray-400">
                                            {{ $admins->firstItem() + $index }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <img src="{{ $adm->profile_photo_url }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                                                <div>
                                                    <span class="font-bold text-gray-900 dark:text-white">{{ $adm->username }}</span>
                                                    @if ($adm->id === Auth::id())
                                                        <span class="ms-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Akun Anda</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap font-mono text-gray-600 dark:text-gray-300">
                                            {{ $adm->no_hp }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                                Admin
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-gray-400 text-[11px]">
                                            {{ $adm->created_at->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-right space-x-2">
                                            <button type="button" @click="openEdit({{ $adm->id }}, @js($adm->username), @js($adm->no_hp))" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer">
                                                Edit
                                            </button>
                                            @if ($adm->id !== Auth::id())
                                                <button type="button" @click="confirmDelete('{{ route('admin.master.admin.destroy', $adm->id) }}', @js($adm->username))" class="text-red-600 dark:text-red-400 font-semibold hover:underline cursor-pointer">
                                                    Hapus
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile List -->
                    <div class="block md:hidden divide-y divide-gray-200 dark:divide-slate-700 p-4 space-y-3.5">
                        @foreach ($admins as $adm)
                            <div class="pt-3.5 first:pt-0 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $adm->profile_photo_url }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $adm->username }}</div>
                                            <div class="text-gray-500 font-mono text-[11px]">{{ $adm->no_hp }}</div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                        Admin
                                    </span>
                                </div>
                                <div class="flex items-center justify-end gap-3 pt-1">
                                    <button type="button" @click="openEdit({{ $adm->id }}, @js($adm->username), @js($adm->no_hp))" class="text-blue-600 dark:text-blue-400 font-semibold underline cursor-pointer">
                                        Edit
                                    </button>
                                    @if ($adm->id !== Auth::id())
                                        <button type="button" @click="confirmDelete('{{ route('admin.master.admin.destroy', $adm->id) }}', @js($adm->username))" class="text-red-600 dark:text-red-400 font-semibold underline cursor-pointer">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($admins->hasPages())
                        <div class="p-3.5 border-t border-gray-200 dark:border-slate-700">
                            {{ $admins->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-10 text-center text-gray-400 dark:text-gray-500 text-xs">
                        Tidak ada akun administrator yang ditemukan.
                    </div>
                @endif

            </div>

        </div>

        <!-- Modal Tambah / Edit Admin -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75" style="display: none;">
            <div class="bg-white dark:bg-[#1e293b] rounded-xl max-w-md w-full overflow-hidden shadow-xl border border-gray-200 dark:border-slate-700" @click.outside="modalOpen = false">
                <div class="p-4 flex items-center justify-between border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white" x-text="isEdit ? 'Edit Administrator' : 'Tambah Administrator Baru'"></h3>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="formUrl" method="POST" enctype="multipart/form-data" class="p-4 space-y-3.5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="!isEdit">

                    <div>
                        <label for="admin_username" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="admin_username" name="username" x-model="formData.username" required
                            placeholder="Contoh: admin_utama"
                            class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label for="admin_nohp" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                            No. HP (WhatsApp) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="admin_nohp" name="no_hp" x-model="formData.no_hp" required
                            placeholder="Contoh: 081234567890"
                            class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label for="admin_password" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                            Password <span x-show="!isEdit" class="text-red-500">*</span> <span x-show="isEdit" class="text-gray-400 font-normal text-[11px]">(Kosongkan jika tidak ingin mengubah)</span>
                        </label>
                        <input type="password" id="admin_password" name="password" :required="!isEdit"
                            placeholder="Minimal 6 karakter"
                            class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                            Foto Profil (Opsional)
                        </label>
                        <input type="file" name="profile_photo" accept="image/*"
                            class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-500 dark:file:bg-slate-700 dark:file:text-gray-200">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-200 dark:border-slate-700">
                        <button type="button" @click="modalOpen = false" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 transition cursor-pointer" x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Admin'">
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hidden Delete Form -->
        <form id="deleteAdminForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>
</x-app-layout>
