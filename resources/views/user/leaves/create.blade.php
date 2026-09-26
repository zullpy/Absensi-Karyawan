<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Form Pengajuan Izin') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Ajukan permohonan izin, sakit, atau cuti kerja kepada pihak HRD/Admin.
                </p>
            </div>
            <a href="{{ route('user.leaves.history') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-gray-200 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Lihat Riwayat Izin</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6">
                    
                    @if ($errors->any())
                        <div class="mb-5 p-3.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs">
                            <div class="font-bold mb-1">Periksa kembali data Anda:</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('user.leaves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <!-- Jenis Izin -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">
                                Jenis Permohonan <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-2.5">
                                <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer text-center group border-gray-200 dark:border-slate-600 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 dark:has-[:checked]:bg-blue-900/30 transition">
                                    <input type="radio" name="type" value="sakit" class="sr-only" {{ old('type') == 'sakit' ? 'checked' : '' }} required>
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Sakit</span>
                                </label>

                                <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer text-center group border-gray-200 dark:border-slate-600 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50 dark:has-[:checked]:bg-amber-900/30 transition">
                                    <input type="radio" name="type" value="izin" class="sr-only" {{ old('type', 'izin') == 'izin' ? 'checked' : '' }}>
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Izin</span>
                                </label>

                                <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer text-center group border-gray-200 dark:border-slate-600 has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50/50 dark:has-[:checked]:bg-purple-900/30 transition">
                                    <input type="radio" name="type" value="cuti" class="sr-only" {{ old('type') == 'cuti' ? 'checked' : '' }}>
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">Cuti</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rentang Tanggal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                                    class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                    Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required
                                    class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                        </div>

                        <!-- Alasan / Keterangan -->
                        <div>
                            <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                Alasan / Keterangan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="reason" name="reason" rows="3" required
                                placeholder="Tuliskan keterangan permohonan izin Anda..."
                                class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('reason') }}</textarea>
                        </div>

                        <!-- Lampiran Berkas -->
                        <div x-data="{ fileName: '' }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                Lampiran Surat / Bukti Dokter (Opsional)
                            </label>
                            <div class="border border-dashed border-gray-300 dark:border-slate-600 rounded-lg p-4 text-center bg-gray-50/50 dark:bg-[#0f172a]/50 relative">
                                <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                                    @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="text-xs text-gray-600 dark:text-gray-300">
                                    <span class="text-blue-600 dark:text-blue-400 font-semibold">Pilih file</span> atau tarik berkas ke sini
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, atau PDF (Maksimal 3 MB)</div>
                                <template x-if="fileName">
                                    <div class="mt-2 text-xs text-blue-700 dark:text-blue-300 font-semibold" x-text="fileName"></div>
                                </template>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-200 dark:border-slate-700">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2 rounded-lg text-xs font-semibold text-white bg-blue-700 hover:bg-blue-800 transition">
                                Kirim Pengajuan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
