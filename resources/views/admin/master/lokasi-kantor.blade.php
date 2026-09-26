<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Master Data - Titik Absensi Kantor') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Tentukan titik koordinat GPS kantor dan batas radius maksimal kehadiran karyawan.
                </p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                Pengaturan GPS
            </span>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="py-6 sm:py-8"
        x-data="{
            officeName: @js($office->name),
            lat: {{ (float) $office->latitude }},
            lng: {{ (float) $office->longitude }},
            radius: {{ (int) $office->radius_meters }},
            map: null,
            marker: null,
            circle: null,
            detectingGps: false,
            satelliteLayer: null,

            initMap() {
                this.$nextTick(() => {
                    const mapEl = document.getElementById('officeMap');
                    if (!mapEl) return;

                    this.map = L.map('officeMap').setView([this.lat, this.lng], 17);

                    // Google Satellite Hybrid Layer (Permanen)
                    this.satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        attribution: '&copy; Google Maps Satelit'
                    }).addTo(this.map);

                    // Custom Office Marker
                    const officeIcon = L.divIcon({
                        className: 'custom-office-pin',
                        html: `<div style='background-color:#2563eb;color:#fff;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 4px 10px rgba(0,0,0,0.3)'>
                                <svg style='width:18px;height:18px' fill='none' stroke='currentColor' viewBox='0 0 24 24' stroke-width='2.5'><path stroke-linecap='round' stroke-linejoin='round' d='M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'/></svg>
                               </div>`,
                        iconSize: [34, 34],
                        iconAnchor: [17, 17]
                    });

                    this.marker = L.marker([this.lat, this.lng], {
                        draggable: true,
                        icon: officeIcon
                    }).addTo(this.map);

                    this.marker.bindPopup('<b>' + this.officeName + '</b><br>Geser pin atau klik peta untuk ubah titik').openPopup();

                    this.circle = L.circle([this.lat, this.lng], {
                        color: '#2563eb',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.25,
                        radius: this.radius
                    }).addTo(this.map);

                    // Drag marker event
                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.updateCoords(pos.lat, pos.lng);
                    });

                    // Click map event
                    this.map.on('click', (e) => {
                        this.updateCoords(e.latlng.lat, e.latlng.lng);
                    });

                    // Auto invalidateSize
                    setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 200);
                    setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 600);
                });
            },

            updateCoords(newLat, newLng) {
                this.lat = parseFloat(newLat.toFixed(7));
                this.lng = parseFloat(newLng.toFixed(7));
                if (this.marker) this.marker.setLatLng([this.lat, this.lng]);
                if (this.circle) this.circle.setLatLng([this.lat, this.lng]);
            },

            updateRadius() {
                if (this.circle) {
                    this.circle.setRadius(parseInt(this.radius) || 50);
                }
            },

            useCurrentLocation() {
                if (!navigator.geolocation) {
                    Swal.fire('Error', 'Browser tidak mendukung geolokasi GPS.', 'error');
                    return;
                }

                this.detectingGps = true;
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.detectingGps = false;
                        const cLat = pos.coords.latitude;
                        const cLng = pos.coords.longitude;
                        this.updateCoords(cLat, cLng);
                        if (this.map) {
                            this.map.setView([cLat, cLng], 18);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Lokasi Terdeteksi',
                            text: `Titik kantor disesuaikan ke posisi GPS Anda (${cLat.toFixed(5)}, ${cLng.toFixed(5)})`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    (err) => {
                        this.detectingGps = false;
                        Swal.fire('GPS Gagal', 'Pastikan izin lokasi aktif di browser Anda. Error: ' + err.message, 'warning');
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            }
        }"
        x-init="initMap()">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Alerts -->
            @if (session('success'))
                <div class="p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div><span class="font-bold">Berhasil:</span> {{ session('success') }}</div>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-3.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs">
                    <div class="font-bold mb-1">Gagal menyimpan data:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                
                <!-- MAP CONTAINER (7 COLS) -->
                <div class="lg:col-span-7 bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="p-3.5 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                            <span class="font-bold text-xs text-gray-900 dark:text-white">Peta Titik Absen Kantor</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                                Mode Satelit
                            </span>
                            <button type="button" @click="useCurrentLocation()" :disabled="detectingGps"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 transition shadow-xs cursor-pointer disabled:opacity-50">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-text="detectingGps ? 'Mencari Lokasi...' : 'Pakai Lokasi Saya Sekarang'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="h-96 w-full" id="officeMap"></div>

                    <div class="p-3 bg-gray-50/75 dark:bg-[#0f172a]/50 border-t border-gray-100 dark:border-slate-700/60 text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
                        <span>Tip: Anda bisa <b>menggeser ikon pin biru</b> atau <b>klik di mana saja pada peta</b> untuk menetapkan lokasi titik absensi.</span>
                    </div>
                </div>

                <!-- FORM SETTING (5 COLS) -->
                <div class="lg:col-span-5 bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-slate-700">
                        Form Pengaturan Titik Absen
                    </h3>

                    <form method="POST" action="{{ route('admin.master.lokasi-kantor.update') }}" class="space-y-4 text-xs">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                Nama Kantor / Lokasi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="officeName" required
                                placeholder="Contoh: Kantor Pusat / Cabang Utama"
                                class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                    Latitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="any" name="latitude" x-model="lat" required
                                    @input="updateCoords(parseFloat(lat), parseFloat(lng))"
                                    class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs font-mono text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                    Longitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="any" name="longitude" x-model="lng" required
                                    @input="updateCoords(parseFloat(lat), parseFloat(lng))"
                                    class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs font-mono text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Radius Toleransi Absensi (Meter) <span class="text-red-500">*</span>
                                </label>
                                <span class="font-bold text-blue-600 dark:text-blue-400 font-mono text-xs" x-text="radius + ' m'"></span>
                            </div>
                            <input type="number" name="radius_meters" min="5" max="10000" x-model="radius" required
                                @input="updateRadius()"
                                placeholder="Contoh: 50"
                                class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs font-mono text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            <span class="text-[11px] text-gray-400 mt-1 block">Karyawan hanya bisa absen jika berada dalam lingkaran radius ini dari titik kantor.</span>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 transition shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Titik Absen Kantor</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
