<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pengajuan Izin') }}
            </h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                Admin Panel
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700/60">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Persetujuan Pengajuan Izin / Cuti</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Verifikasi dan setujui permohonan izin, sakit, dan cuti dari karyawan.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center bg-gray-50/50 dark:bg-gray-800/50">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Halaman verifikasi pengajuan izin siap dikembangkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
