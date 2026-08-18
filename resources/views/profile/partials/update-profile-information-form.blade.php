<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Perbarui informasi akun, nomor handphone, dan foto profil Anda.') }}
        </p>
    </header>

    <div x-data="profilePhotoEditor({
        defaultUrl: '{{ $user->profile_photo_url }}',
        avatarFallback: 'https://ui-avatars.com/api/?name={{ urlencode($user->username ?? 'User') }}&color=2563eb&background=dbeafe&bold=true'
    })">
        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            @method('patch')

            <!-- Hidden input for remove photo flag -->
            <input type="hidden" name="remove_photo" :value="removePhoto ? 1 : 0">
            <!-- Hidden input for cropped base64 data -->
            <input type="hidden" name="cropped_photo_base64" :value="croppedBase64">

            <!-- Foto Profil Section -->
            <div>
                <x-input-label :value="__('Foto Profil')" class="mb-2" />
                
                <div class="flex items-center gap-5">
                    <!-- Avatar Preview -->
                    <div class="relative group cursor-pointer" @click="$refs.photoInput.click()">
                        <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-blue-500/30 dark:border-blue-400/30 shadow-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center transition group-hover:opacity-90">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" alt="Preview Foto" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!photoPreview && !removePhoto">
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!photoPreview && removePhoto">
                                <img :src="avatarFallback" alt="{{ $user->username }}" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div class="absolute inset-0 rounded-full bg-black/35 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Action Buttons & File Input -->
                    <div class="space-y-2">
                        <input 
                            type="file" 
                            id="profile_photo" 
                            name="profile_photo" 
                            accept="image/png, image/jpeg, image/jpg, image/webp" 
                            class="hidden" 
                            x-ref="photoInput"
                            @change="onFileSelected($event)"
                        />

                        <div class="flex flex-wrap items-center gap-2">
                            <button 
                                type="button" 
                                @click="$refs.photoInput.click()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 text-xs font-semibold rounded-lg transition active:scale-95"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('Pilih Foto') }}
                            </button>

                            <button 
                                type="button" 
                                x-show="photoPreview"
                                @click="openEditorWithCurrent()" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 text-xs font-semibold rounded-lg transition active:scale-95"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ __('Sesuaikan') }}
                            </button>

                            @if ($user->profile_photo)
                                <button 
                                    type="button" 
                                    x-show="!removePhoto || photoPreview"
                                    @click="clearPhoto()" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/50 text-xs font-semibold rounded-lg transition active:scale-95"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    {{ __('Hapus') }}
                                </button>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">JPG, PNG, atau WEBP. Foto dapat di-zoom & disesuaikan sebelum disimpan.</p>
                    </div>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
            </div>

            <!-- Username -->
            <div>
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" name="username" type="text" class="mt-1 block w-fit p-2" :value="old('username', $user->username)" required autofocus autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>

            <!-- Nomor Handphone -->
            <div>
                <x-input-label for="no_hp" :value="__('Nomor Handphone')" />
                <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-fit p-2" :value="old('no_hp', $user->no_hp)" required autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2500)"
                        class="text-sm font-medium text-green-600 dark:text-green-400 flex items-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('Profil berhasil disimpan.') }}
                    </p>
                @endif
            </div>
        </form>

        <!-- ===== CROP & ZOOM PHOTO MODAL ===== -->
        <div 
            x-show="isModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.away="closeModal()"
                class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col max-h-[90vh]"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            >
                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Sesuaikan Foto Profil</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Geser & perbesar untuk mengatur posisi foto</p>
                    </div>
                    <button 
                        type="button" 
                        @click="closeModal()"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Cropper Canvas Container -->
                <div class="relative bg-slate-950 flex items-center justify-center overflow-hidden" style="height: 320px;">
                    <img 
                        x-ref="imageToCrop" 
                        :src="rawImageSrc" 
                        alt="Crop target" 
                        class="max-w-full block"
                        style="max-height: 320px;"
                    />
                </div>

                <!-- Controls: Zoom & Rotate -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/60 space-y-3">
                    <!-- Zoom Slider -->
                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            @click="zoom(-0.1)"
                            title="Zoom Out"
                            class="p-1.5 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        
                        <input 
                            type="range" 
                            min="0.1" 
                            max="3" 
                            step="0.05" 
                            x-model="zoomValue" 
                            @input="onZoomChange()"
                            class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-600"
                        />

                        <button 
                            type="button" 
                            @click="zoom(0.1)"
                            title="Zoom In"
                            class="p-1.5 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>

                    <!-- Rotate & Reset Toolbar -->
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-1">
                            <button 
                                type="button" 
                                @click="rotate(-90)"
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                90° Kiri
                            </button>

                            <button 
                                type="button" 
                                @click="rotate(90)"
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" />
                                </svg>
                                90° Kanan
                            </button>
                        </div>

                        <button 
                            type="button" 
                            @click="resetCropper()"
                            class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition"
                        >
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="px-5 py-3.5 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        @click="closeModal()"
                        class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-xl transition active:scale-95"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        @click="applyCrop()"
                        class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/30 rounded-xl transition active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Gunakan Foto
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<style>
/* Custom Circular Cropper mask */
.cropper-view-box,
.cropper-face {
    border-radius: 50% !important;
}
.cropper-view-box {
    outline: 2px solid #3b82f6 !important;
    outline-color: rgba(59, 130, 246, 0.9) !important;
}
.cropper-line, .cropper-point {
    background-color: #3b82f6 !important;
}
</style>

<script>
(function() {
    function registerEditor() {
        if (!window.Alpine) return;
        window.Alpine.data('profilePhotoEditor', (config) => ({
            photoPreview: null,
            removePhoto: false,
            croppedBase64: null,
            rawImageSrc: '',
            isModalOpen: false,
            cropper: null,
            zoomValue: 1,
            defaultUrl: config.defaultUrl,
            avatarFallback: config.avatarFallback,

            onFileSelected(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Validate file type
                if (!file.type.match(/^image\/(jpeg|png|jpg|webp)$/i)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Format Tidak Didukung',
                            text: 'Silakan pilih gambar dengan format JPG, PNG, atau WEBP.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert('Format gambar tidak didukung.');
                    }
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.rawImageSrc = e.target.result;
                    this.isModalOpen = true;
                    this.$nextTick(() => {
                        this.initCropper();
                    });
                };
                reader.readAsDataURL(file);
            },

            openEditorWithCurrent() {
                if (this.rawImageSrc) {
                    this.isModalOpen = true;
                    this.$nextTick(() => {
                        this.initCropper();
                    });
                }
            },

            initCropper() {
                if (this.cropper) {
                    this.cropper.destroy();
                }

                const imageElement = this.$refs.imageToCrop;
                if (!imageElement) return;

                this.cropper = new Cropper(imageElement, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.85,
                    restore: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    toggleDragModeOnDblclick: false,
                    ready: () => {
                        this.zoomValue = 1;
                    },
                    zoom: (e) => {
                        if (e.detail && e.detail.ratio) {
                            this.zoomValue = Math.min(Math.max(e.detail.ratio, 0.1), 3);
                        }
                    }
                });
            },

            zoom(step) {
                if (!this.cropper) return;
                this.cropper.zoom(step);
            },

            onZoomChange() {
                if (!this.cropper) return;
                this.cropper.zoomTo(parseFloat(this.zoomValue));
            },

            rotate(deg) {
                if (!this.cropper) return;
                this.cropper.rotate(deg);
            },

            resetCropper() {
                if (!this.cropper) return;
                this.cropper.reset();
                this.zoomValue = 1;
            },

            closeModal() {
                this.isModalOpen = false;
                if (!this.croppedBase64 && this.$refs.photoInput) {
                    this.$refs.photoInput.value = '';
                }
                if (this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }
            },

            applyCrop() {
                if (!this.cropper) return;

                // Generate circular cropped 512x512 canvas
                const croppedCanvas = this.cropper.getCroppedCanvas({
                    width: 512,
                    height: 512,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                if (!croppedCanvas) return;

                const base64Data = croppedCanvas.toDataURL('image/jpeg', 0.92);
                this.photoPreview = base64Data;
                this.croppedBase64 = base64Data;
                this.removePhoto = false;

                // Clear the raw file input so backend strictly processes the cropped base64
                if (this.$refs.photoInput) {
                    this.$refs.photoInput.value = '';
                }

                this.closeModal();
            },

            clearPhoto() {
                this.photoPreview = null;
                this.croppedBase64 = null;
                this.rawImageSrc = '';
                this.removePhoto = true;
                if (this.$refs.photoInput) {
                    this.$refs.photoInput.value = '';
                }
            }
        }));
    }

    if (window.Alpine) {
        registerEditor();
    } else {
        document.addEventListener('alpine:init', registerEditor);
    }
})();
</script>
@endpush
