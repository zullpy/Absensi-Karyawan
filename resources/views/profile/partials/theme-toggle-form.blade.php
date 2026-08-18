<section class="space-y-4" x-data="{
    currentTheme: localStorage.getItem('hadirin-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
    setTheme(mode) {
        this.currentTheme = mode;
        if (mode === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('hadirin-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('hadirin-theme', 'light');
        }
    }
}">
    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            {{ __('Tema & Tampilan') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Pilih tampilan tema terang atau gelap sesuai kenyamanan Anda.') }}
        </p>
    </header>

    <div class="grid grid-cols-2 gap-3 max-w-md pt-2">
        <!-- Light Mode Button -->
        <button 
            type="button" 
            @click="setTheme('light')"
            :class="currentTheme === 'light' 
                ? 'border-blue-600 dark:border-blue-500 bg-blue-50/50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 shadow-sm' 
                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600'"
            class="flex items-center gap-3 p-3.5 rounded-xl border transition cursor-pointer text-left group"
        >
            <div 
                :class="currentTheme === 'light' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                class="w-9 h-9 rounded-lg flex items-center justify-center transition shrink-0"
            >
                <!-- Sun Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="4" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold">Mode Terang</p>
            </div>
        </button>

        <!-- Dark Mode Button -->
        <button 
            type="button" 
            @click="setTheme('dark')"
            :class="currentTheme === 'dark' 
                ? 'border-blue-600 dark:border-blue-500 bg-blue-50/50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 ring-2 ring-blue-500/20 shadow-sm' 
                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600'"
            class="flex items-center gap-3 p-3.5 rounded-xl border transition cursor-pointer text-left group"
        >
            <div 
                :class="currentTheme === 'dark' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                class="w-9 h-9 rounded-lg flex items-center justify-center transition shrink-0"
            >
                <!-- Moon Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold">Mode Gelap</p>
            </div>
        </button>
    </div>
</section>
