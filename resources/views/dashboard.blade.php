<x-app-layout>
    <!-- Direct Leaflet CSS Backup -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    @pushOnce('styles')
        <!-- Leaflet Map CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            #liveMap {
                height: 100% !important;
                min-height: 280px !important;
                width: 100% !important;
            }
            .leaflet-container {
                height: 100% !important;
                width: 100% !important;
                background-color: #0f172a !important;
            }
            .custom-office-pin, .custom-live-user-marker {
                background: none !important;
                border: none !important;
            }
            /* Clean Scanner Corner Guides */
            .scanner-corner {
                position: absolute;
                width: 28px;
                height: 28px;
                border-color: #ffffff;
                z-index: 20;
                pointer-events: none;
            }
            .scanner-corner-tl {
                top: 14px;
                left: 14px;
                border-top: 3.5px solid #ffffff;
                border-left: 3.5px solid #ffffff;
            }
            .scanner-corner-tr {
                top: 14px;
                right: 14px;
                border-top: 3.5px solid #ffffff;
                border-right: 3.5px solid #ffffff;
            }
            .scanner-corner-bl {
                bottom: 14px;
                left: 14px;
                border-bottom: 3.5px solid #ffffff;
                border-left: 3.5px solid #ffffff;
            }
            .scanner-corner-br {
                bottom: 14px;
                right: 14px;
                border-bottom: 3.5px solid #ffffff;
                border-right: 3.5px solid #ffffff;
            }
            .shutter-effect {
                animation: shutterFlash 0.25s ease-out;
            }
            @keyframes shutterFlash {
                0% { opacity: 0.8; background: #ffffff; }
                100% { opacity: 0; background: transparent; }
            }
            @keyframes leafletPulse {
                0% { transform: scale(0.6); opacity: 0.9; }
                100% { transform: scale(2.5); opacity: 0; }
            }
        </style>
    @endpushOnce

    @pushOnce('scripts')
        <!-- Leaflet Map JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <!-- Face Detection Engine (Tracking.js + Viola-Jones Haar Cascade) -->
        <script src="{{ asset('vendor/tracking/tracking-min.js') }}"></script>
        <script src="{{ asset('vendor/tracking/face-min.js') }}"></script>
    @endpushOnce

    <div class="py-6 sm:py-8" x-data="attendanceHandler()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Main Content Container -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl p-5 sm:p-7 shadow-sm border border-gray-200 dark:border-slate-700/70">
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    
                    <!-- LEFT COLUMN: WEBCAM VIEW -->
                    <div class="md:col-span-5 flex flex-col gap-3.5">
                        
                        <!-- Camera Scanner Container -->
                        <div class="relative w-full aspect-square bg-[#0b132b] rounded-lg overflow-hidden border-2 border-dashed border-gray-300 dark:border-slate-600 flex items-center justify-center">
                            <!-- Corner Frame Markers -->
                            <div class="scanner-corner scanner-corner-tl" :style="isFaceDetected ? 'border-color: #10b981 !important' : ''"></div>
                            <div class="scanner-corner scanner-corner-tr" :style="isFaceDetected ? 'border-color: #10b981 !important' : ''"></div>
                            <div class="scanner-corner scanner-corner-bl" :style="isFaceDetected ? 'border-color: #10b981 !important' : ''"></div>
                            <div class="scanner-corner scanner-corner-br" :style="isFaceDetected ? 'border-color: #10b981 !important' : ''"></div>

                            <!-- Realtime Face Status HUD Badge -->
                            <div x-show="cameraActive" class="absolute top-3 left-5 right-5 z-20 flex items-center justify-between pointer-events-none transition-all">
                                <div class="inline-flex items-center gap-1.5 px-3 ml-14 py-1.5 rounded-full text-xs font-bold shadow-md backdrop-blur-md transition-colors"
                                     :class="isFaceDetected ? 'bg-emerald-600/90 text-white' : 'bg-red-600/90 text-white'">
                                    <span class="w-2 h-2 rounded-full bg-white" :class="isFaceDetected ? 'animate-ping' : ''"></span>
                                    <span x-text="isFaceDetected ? 'Wajah Terdeteksi (Siap Absen)' : 'Wajah Belum Terdeteksi'"></span>
                                </div>
                            </div>

                            <!-- Shutter Flash -->
                            <div id="shutterFlash" class="absolute inset-0 z-30 pointer-events-none opacity-0"></div>

                            <!-- Live Video Stream -->
                            <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover -scale-x-100"></video>
                            
                            <!-- Hidden Snapshot Canvas -->
                            <canvas id="snapshotCanvas" class="hidden"></canvas>

                            <!-- Camera Inactive State -->
                            <div x-show="!cameraActive" class="absolute inset-0 bg-[#0f172a] flex flex-col items-center justify-center p-5 text-center z-10" style="display: none;">
                                <svg class="w-10 h-10 text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                                </svg>
                                <p class="text-xs text-gray-300 font-semibold mb-1" x-text="cameraError ? 'Akses Kamera Terkendala' : 'Kamera belum aktif'"></p>
                                <p class="text-[11px] text-red-400 mb-3 max-w-xs" x-show="cameraError" x-text="cameraError"></p>
                                <button type="button" @click="initCamera()" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition cursor-pointer">
                                    Coba Nyalakan Kamera
                                </button>
                            </div>
                        </div>

                        <!-- Face Warning Alert if no face detected while camera active -->
                        <div x-show="cameraActive && !isFaceDetected" class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-2 transition-all">
                            <svg class="w-4 h-4 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                            <span>Wajah belum terdeteksi. Posisikan wajah Anda ke kamera untuk melakukan absen.</span>
                        </div>

                        <!-- Tombol Aksi Absen Masuk & Keluar -->
                        <div class="grid grid-cols-2 gap-2.5 pt-1">
                            <button type="button"
                                @click="submitAttendance('in')"
                                :disabled="loading || (hasAttendanceToday && attendanceData.time_in) || !isWithinRadius || (cameraActive && !isFaceDetected)"
                                class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg font-semibold text-xs text-white bg-blue-700 hover:bg-blue-800 active:bg-blue-900 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                <span>Absen Masuk</span>
                            </button>

                            <button type="button"
                                @click="submitAttendance('out')"
                                :disabled="loading || !hasAttendanceToday || attendanceData.time_out || !isWithinRadius || (cameraActive && !isFaceDetected)"
                                class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg font-semibold text-xs text-white bg-amber-700 hover:bg-amber-800 active:bg-amber-900 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Absen Keluar</span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: DATE, GPS RADIUS, CARDS & QUICK ACTIONS -->
                    <div class="md:col-span-7 flex flex-col gap-4">
                        
                        <!-- Header Info: Tanggal, Info Titik Kantor & Lokasi Anda -->
                        <div class="pb-1 space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm sm:text-base font-bold text-gray-800 dark:text-gray-100">
                                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-mono text-xs font-bold shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                        <span x-text="liveClockWib"></span> WIB
                                    </span>

                                    <!-- Compact Status Notifikasi Pengingat 15 Menit -->
                                    <template x-if="pushEnabled">
                                        <button type="button" @click="sendTestNotification()" :disabled="sendingTest"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-semibold text-xs shadow-xs cursor-pointer hover:bg-emerald-100 transition"
                                            title="Pengingat 15 Menit Aktif - Klik untuk tes notifikasi">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span x-text="sendingTest ? 'Mengirim...' : 'Pengingat Aktif (Tes)'"></span>
                                        </button>
                                    </template>
                                    <template x-if="!pushEnabled">
                                        <button type="button" @click="requestNotificationPermission()"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 font-semibold text-xs shadow-xs cursor-pointer hover:bg-amber-100 transition"
                                            title="Klik untuk izinkan pengingat absensi">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                            <span>Izin Notifikasi</span>
                                        </button>
                                    </template>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Titik Kantor: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $officeSetting->name }}</span>
                                    <span class="font-semibold text-gray-600 dark:text-gray-300">(Max {{ $officeSetting->radius_meters }}m)</span>
                                </div>
                            </div>

                            <!-- Live Realtime Interactive Map Container -->
                            <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-900 shadow-sm relative">
                                <!-- Top Map HUD Status Overlay -->
                                <div class="absolute top-2.5 left-2.5 right-2.5 z-[1000] flex flex-wrap items-center justify-between gap-2 pointer-events-none">
                                    <!-- GPS Status Beacon -->
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/95 dark:bg-slate-900/95 backdrop-blur-md shadow-sm border border-gray-200/80 dark:border-slate-700 pointer-events-auto text-xs font-semibold">
                                        <span class="relative flex h-2.5 w-2.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="isLocationLoaded ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="isLocationLoaded ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                        </span>
                                        <span class="text-gray-800 dark:text-gray-200" x-text="isLocationLoaded ? 'GPS Realtime Terhubung' : 'Mencari Koordinat GPS...'"></span>
                                    </div>

                                    <!-- Distance & Radius Badge -->
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg shadow-sm border pointer-events-auto text-xs font-bold transition-all"
                                        :class="isWithinRadius ? 'bg-emerald-600 text-white border-emerald-700' : 'bg-red-600 text-white border-red-700'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="isWithinRadius ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                                        </svg>
                                        <span x-text="isLocationLoaded ? (isWithinRadius ? 'Dalam Radius (' + formattedDistance + ' m)' : 'Di Luar Radius (' + formattedDistance + ' m)') : 'Menghitung Jarak...'"></span>
                                    </div>
                                </div>

                                <!-- The Leaflet Map Element -->
                                <div id="liveMap" class="w-full h-64 sm:h-72"></div>
                            </div>

                            <!-- Diagnostic Location Error Alert -->
                            <div x-show="locationError" class="p-2.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs flex items-center gap-2" style="display: none;">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                <span x-text="locationError"></span>
                            </div>
                        </div>

                        <!-- 3 Status Cards (Absen Masuk, Absen Keluar, Koordinat Absen) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            
                            <!-- Card Absen Masuk -->
                            <div class="bg-[#1e40af] dark:bg-[#1e3a8a] text-white rounded-lg p-3.5 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-bold">Absen Masuk</div>
                                    <div class="text-xs text-blue-100 mt-0.5" x-text="attendanceData.time_in ? (formatTime(attendanceData.time_in) + (attendanceData.status === 'late' ? ' (Terlambat)' : '')) : 'Belum Absen'"></div>
                                </div>
                                <svg class="w-5 h-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </div>

                            <!-- Card Absen Keluar -->
                            <div class="bg-[#9a3412] dark:bg-[#7c2d12] text-white rounded-lg p-3.5 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-bold">Absen Keluar</div>
                                    <div class="text-xs text-orange-100 mt-0.5" x-text="attendanceData.time_out ? formatTime(attendanceData.time_out) : 'Belum Absen'"></div>
                                </div>
                                <svg class="w-5 h-5 text-orange-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            </div>

                            <!-- Card Koordinat Absen -->
                            <div class="bg-[#6b21a8] dark:bg-[#581c87] text-white rounded-lg p-3.5 flex items-center justify-between">
                                <div class="truncate pe-1">
                                    <div class="text-sm font-bold">Koordinat Absen</div>
                                    <template x-if="attendanceData.lat_in">
                                        <a :href="'https://www.google.com/maps?q=' + attendanceData.lat_in + ',' + attendanceData.long_in" target="_blank" class="text-xs text-purple-100 hover:underline block truncate mt-0.5" x-text="attendanceData.lat_in.toFixed(4) + ', ' + attendanceData.long_in.toFixed(4)"></a>
                                    </template>
                                    <template x-if="!attendanceData.lat_in">
                                        <div class="text-xs text-purple-100 mt-0.5">Belum Absen</div>
                                    </template>
                                </div>
                                <svg class="w-5 h-5 text-purple-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>

                        </div>

                        <!-- 3 Quick Action Buttons (Ajukan Izin, Riwayat Absen, Riwayat Izin) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <!-- Ajukan Izin -->
                            <a href="{{ route('user.leaves.create') }}"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold text-xs text-white bg-[#d97706] hover:bg-[#b45309] transition">
                                <span>Ajukan Izin</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </a>

                            <!-- Riwayat Absen -->
                            <a href="{{ route('user.attendance.history') }}"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold text-xs text-white bg-[#2563eb] hover:bg-[#1d4ed8] transition">
                                <span>Riwayat Absen</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </a>

                            <!-- Riwayat Izin -->
                            <a href="{{ route('user.leaves.history') }}"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold text-xs text-white bg-[#4f46e5] hover:bg-[#4338ca] transition">
                                <span>Riwayat Izin</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function attendanceHandler() {
            return {
                cameraActive: false,
                loading: false,
                videoStream: null,
                hasAttendanceToday: {{ isset($todayAttendance) ? 'true' : 'false' }},
                attendanceData: {
                    time_in: '{{ $todayAttendance?->time_in ?? "" }}',
                    time_out: '{{ $todayAttendance?->time_out ?? "" }}',
                    status: '{{ $todayAttendance?->status ?? "" }}',
                    lat_in: {{ $todayAttendance?->lat_in ? (float)$todayAttendance->lat_in : 'null' }},
                    long_in: {{ $todayAttendance?->long_in ? (float)$todayAttendance->long_in : 'null' }},
                },
                officeName: @js($officeSetting->name),
                officeLat: {{ (float) $officeSetting->latitude }},
                officeLng: {{ (float) $officeSetting->longitude }},
                officeRadius: {{ (int) $officeSetting->radius_meters }},
                currentCoords: null,
                distanceToOffice: null,
                isLocationLoaded: false,
                isWithinRadius: false,
                formattedDistance: 0,
                mapInstance: null,
                userMarker: null,
                officeMarker: null,
                officeCircle: null,
                distanceLine: null,
                hasCenteredInitially: false,
                cameraError: '',
                locationError: '',
                satelliteLayer: null,
                isFaceDetected: false,
                isScanningFace: false,
                faceCheckInterval: null,
                tempDetectionCanvas: null,
                liveClockWib: '',
                pushEnabled: false,
                sendingTest: false,

                init() {
                    this.initLiveClock();
                    this.initPushNotification();
                    this.initServiceWorkerMessageListener();
                    this.checkUrlForNotificationPopup();
                    this.initCamera();
                    this.initGeolocation();
                    this.$nextTick(() => {
                        this.renderMap();
                    });
                },

                initLiveClock() {
                    const update = () => {
                        const now = new Date();
                        const timeStr = new Intl.DateTimeFormat('id-ID', {
                            timeZone: 'Asia/Jakarta',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        }).format(now).replace(/\./g, ':');
                        this.liveClockWib = timeStr;
                    };
                    update();
                    setInterval(update, 1000);
                },

                async initCamera() {
                    const video = document.getElementById('webcam');
                    this.cameraError = '';

                    const isLocal = location.hostname === 'localhost' || location.hostname === '127.0.0.1';
                    if (!window.isSecureContext && !isLocal) {
                        this.cameraError = 'Browser memblokir kamera di HTTP IP (' + location.hostname + '). Buka via localhost atau aktifkan izin di chrome://flags';
                        this.cameraActive = false;
                        return;
                    }

                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        this.cameraError = 'Browser tidak mengizinkan akses kamera pada koneksi HTTP ini.';
                        this.cameraActive = false;
                        return;
                    }

                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                width: { ideal: 640 },
                                height: { ideal: 640 },
                                facingMode: 'user'
                            },
                            audio: false
                        });
                        this.videoStream = stream;
                        video.srcObject = stream;
                        this.cameraActive = true;

                        // Start continuous face detection when video begins playback
                        video.onloadeddata = () => {
                            this.startFaceTracking();
                        };
                        if (video.readyState >= 2) {
                            this.startFaceTracking();
                        }
                    } catch (err) {
                        console.error('Camera error:', err);
                        this.cameraActive = false;
                        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                            this.cameraError = 'Izin kamera ditolak di browser. Klik ikon gembok / izin di sebelah URL browser Anda untuk mengizinkan.';
                        } else if (err.name === 'NotFoundError') {
                            this.cameraError = 'Perangkat kamera tidak ditemukan.';
                        } else {
                            this.cameraError = err.message || 'Kamera gagal diakses.';
                        }
                    }
                },

                initGeolocation() {
                    this.locationError = '';

                    const isLocal = location.hostname === 'localhost' || location.hostname === '127.0.0.1';
                    if (!window.isSecureContext && !isLocal) {
                        this.locationError = 'GPS diblokir di HTTP IP non-localhost.';
                        return;
                    }

                    if (!navigator.geolocation) {
                        this.locationError = 'Perangkat tidak mendukung geolokasi GPS.';
                        return;
                    }

                    navigator.geolocation.watchPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            this.currentCoords = [lat, lng];
                            this.isLocationLoaded = true;
                            this.locationError = '';

                            this.distanceToOffice = this.calculateDistance(lat, lng, this.officeLat, this.officeLng);
                            this.formattedDistance = this.distanceToOffice.toFixed(1);
                            this.isWithinRadius = this.distanceToOffice <= this.officeRadius;

                            this.updateMap();
                        },
                        (error) => {
                            console.error('GPS error:', error);
                            this.isLocationLoaded = false;
                            this.isWithinRadius = false;
                            if (error.code === 1) { // PERMISSION_DENIED
                                this.locationError = 'Izin GPS ditolak di browser.';
                            } else if (error.code === 2) {
                                this.locationError = 'Sinyal GPS tidak ditemukan.';
                            } else if (error.code === 3) {
                                this.locationError = 'Waktu pencarian GPS habis.';
                            } else {
                                this.locationError = error.message;
                            }
                        },
                        {
                            enableHighAccuracy: true,
                            maximumAge: 3000,
                            timeout: 10000
                        }
                    );
                },

                calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const dLat = (lat2 - lat1) * (Math.PI / 180);
                    const dLon = (lon2 - lon1) * (Math.PI / 180);
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                              Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                              Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                },

                renderMap() {
                    const mapEl = document.getElementById('liveMap');
                    if (!mapEl || this.mapInstance) return;

                    const centerLat = this.currentCoords ? this.currentCoords[0] : this.officeLat;
                    const centerLng = this.currentCoords ? this.currentCoords[1] : this.officeLng;

                    this.mapInstance = L.map('liveMap', {
                        zoomControl: false
                    }).setView([centerLat, centerLng], 16);

                    L.control.zoom({ position: 'bottomleft' }).addTo(this.mapInstance);

                    // Google Maps Hybrid Satellite Layer (Permanen Foto Satelit + Label Jalan)
                    this.satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        attribution: '&copy; Google Maps Satelit'
                    }).addTo(this.mapInstance);

                    // Custom Office Marker
                    const officeIcon = L.divIcon({
                        className: 'custom-office-pin',
                        html: `<div style='background-color:#2563eb;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 3px 8px rgba(0,0,0,0.35)'>
                                <svg style='width:16px;height:16px' fill='none' stroke='currentColor' viewBox='0 0 24 24' stroke-width='2.5'><path stroke-linecap='round' stroke-linejoin='round' d='M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'/></svg>
                               </div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    });

                    this.officeMarker = L.marker([this.officeLat, this.officeLng], { icon: officeIcon }).addTo(this.mapInstance);
                    this.officeMarker.bindPopup('<b>' + this.officeName + '</b><br>Titik Absensi (Radius: ' + this.officeRadius + ' m)');

                    // Office Radius Circle
                    this.officeCircle = L.circle([this.officeLat, this.officeLng], {
                        color: '#2563eb',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.22,
                        radius: this.officeRadius
                    }).addTo(this.mapInstance);

                    // Invalidate size to guarantee all map tiles render properly
                    setTimeout(() => {
                        if (this.mapInstance) this.mapInstance.invalidateSize();
                    }, 150);
                    setTimeout(() => {
                        if (this.mapInstance) this.mapInstance.invalidateSize();
                    }, 500);

                    if (this.currentCoords) {
                        this.updateMap();
                    }
                },

                updateMap() {
                    if (!this.mapInstance || !this.currentCoords) return;

                    const lat = this.currentCoords[0];
                    const lng = this.currentCoords[1];

                    // Trigger Leaflet redraw
                    this.mapInstance.invalidateSize();

                    if (this.userMarker) {
                        this.userMarker.setLatLng([lat, lng]);
                    } else {
                        const userIcon = L.divIcon({
                            className: 'custom-live-user-marker',
                            html: `<div style="position:relative;width:24px;height:24px">
                                    <div style="position:absolute;width:24px;height:24px;border-radius:50%;background:#10b981;opacity:0.5;animation:leafletPulse 1.8s infinite"></div>
                                    <div style="position:absolute;top:5px;left:5px;width:14px;height:14px;border-radius:50%;background:#10b981;border:2.5px solid #ffffff;box-shadow:0 0 8px rgba(0,0,0,0.4)"></div>
                                   </div>`,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        });
                        this.userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(this.mapInstance);
                        this.userMarker.bindPopup('<b>Posisi Anda (Realtime)</b>');
                    }

                    // Update connecting line
                    if (this.distanceLine) {
                        this.distanceLine.setLatLngs([
                            [lat, lng],
                            [this.officeLat, this.officeLng]
                        ]);
                    }

                    // Smart map framing on initial GPS fix
                    if (!this.hasCenteredInitially) {
                        this.hasCenteredInitially = true;
                        if (this.distanceToOffice && this.distanceToOffice <= 3000) {
                            this.fitAll();
                        } else {
                            // If far away (e.g. office still set to Bandung), focus on user's current location
                            this.mapInstance.setView([lat, lng], 16);
                        }
                    }
                },

                centerOnUser() {
                    if (this.mapInstance && this.currentCoords) {
                        this.mapInstance.invalidateSize();
                        this.mapInstance.flyTo(this.currentCoords, 17, { duration: 0.8 });
                    }
                },

                centerOnOffice() {
                    if (this.mapInstance) {
                        this.mapInstance.invalidateSize();
                        this.mapInstance.flyTo([this.officeLat, this.officeLng], 17, { duration: 0.8 });
                    }
                },

                fitAll() {
                    if (!this.mapInstance) return;
                    this.mapInstance.invalidateSize();
                    const group = L.featureGroup([
                        this.officeCircle,
                        this.userMarker || this.officeMarker
                    ]);
                    this.mapInstance.fitBounds(group.getBounds().pad(0.2));
                },

                startFaceTracking() {
                    if (this.faceCheckInterval) clearInterval(this.faceCheckInterval);
                    this.faceCheckInterval = setInterval(async () => {
                        if (!this.cameraActive || this.loading || this.isScanningFace) return;
                        const video = document.getElementById('webcam');
                        if (!video || video.readyState < 2 || video.paused || video.ended) return;

                        this.isScanningFace = true;
                        try {
                            const res = await this.detectFaceInElement(video);
                            this.isFaceDetected = res.detected;
                        } catch (e) {
                            // ignore tracking tick error
                        } finally {
                            this.isScanningFace = false;
                        }
                    }, 600);
                },

                async detectFaceInElement(source) {
                    if (!source) return { detected: false };

                    // 1. Native Hardware-Accelerated Browser FaceDetector API (Chrome, Edge, Chrome Android)
                    if ('FaceDetector' in window) {
                        try {
                            const nativeDetector = new window.FaceDetector({ fastMode: true, maxDetectedFaces: 2 });
                            const faces = await nativeDetector.detect(source);
                            if (faces && faces.length > 0) return { detected: true, faces };
                        } catch (e) {}
                    }

                    // 2. Tracking.js Viola-Jones Classifier (HAAR Cascade AI)
                    if (window.tracking && window.tracking.ViolaJones && window.tracking.ViolaJones.classifiers.face) {
                        try {
                            const w = 320;
                            const h = source.videoHeight && source.videoWidth 
                                ? Math.round((source.videoHeight / source.videoWidth) * w) 
                                : (source.height && source.width ? Math.round((source.height / source.width) * w) : 240);

                            if (!this.tempDetectionCanvas) {
                                this.tempDetectionCanvas = document.createElement('canvas');
                            }
                            this.tempDetectionCanvas.width = w;
                            this.tempDetectionCanvas.height = h;
                            const ctx = this.tempDetectionCanvas.getContext('2d', { willReadFrequently: true });
                            ctx.drawImage(source, 0, 0, w, h);
                            const imgData = ctx.getImageData(0, 0, w, h);

                            const detected = window.tracking.ViolaJones.detect(
                                imgData.data, w, h, 1.25, 1.25, 1.5, 0.05,
                                window.tracking.ViolaJones.classifiers.face
                            );

                            if (detected && detected.length > 0) {
                                return { detected: true, faces: detected };
                            }
                        } catch (e) {
                            console.warn('Face detection error:', e);
                        }
                    }

                    return { detected: false, faces: [] };
                },

                captureSelfie() {
                    const video = document.getElementById('webcam');
                    const canvas = document.getElementById('snapshotCanvas');
                    const flash = document.getElementById('shutterFlash');

                    if (!video || !canvas) return null;

                    flash.classList.add('shutter-effect');
                    setTimeout(() => flash.classList.remove('shutter-effect'), 250);

                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');

                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    return canvas.toDataURL('image/jpeg', 0.85);
                },

                async submitAttendance(type) {
                    if (!this.isWithinRadius) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Di Luar Radius Kantor',
                            text: `Posisi Anda ${this.formattedDistance} meter dari kantor. Maksimal ${this.officeRadius} meter.`,
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }

                    if (!this.cameraActive) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kamera Belum Aktif',
                            text: 'Silakan aktifkan kamera terlebih dahulu untuk melakukan presensi.',
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }

                    const canvas = document.getElementById('snapshotCanvas');
                    const selfieData = this.captureSelfie();
                    if (!selfieData) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kamera Error',
                            text: 'Gagal mengambil foto selfie.',
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }

                    // VALIDASI KETAT: Foto WAJIB terdeteksi wajahnya
                    this.loading = true;
                    const faceCheck = await this.detectFaceInElement(canvas);
                    if (!faceCheck.detected) {
                        this.loading = false;
                        this.isFaceDetected = false;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Wajah Tidak Terdeteksi!',
                            html: `<div class="text-left text-xs text-gray-600 dark:text-gray-300 space-y-2">
                                    <p>Presensi <b>tidak dapat dilakukan</b> karena tidak terdeteksi adanya wajah pada foto selfie Anda.</p>
                                    <div class="bg-amber-50 dark:bg-amber-950/40 p-2.5 rounded-lg border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200">
                                        <p class="font-bold mb-1">Ketentuan Foto Absensi:</p>
                                        <ul class="list-disc list-inside space-y-0.5">
                                            <li>Wajah wajib terlihat jelas dan menghadap ke kamera.</li>
                                            <li>Pastikan pencahayaan ruangan cukup terang.</li>
                                            <li>Jangan menutupi wajah dengan tangan, masker, atau benda lain.</li>
                                        </ul>
                                    </div>
                                   </div>`,
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'Coba Lagi'
                        });
                        return;
                    }

                    const endpoint = type === 'in' ? '{{ route("user.attendance.clock-in") }}' : '{{ route("user.attendance.clock-out") }}';
                    const payload = {
                        latitude: this.currentCoords[0],
                        longitude: this.currentCoords[1],
                        image: selfieData,
                        _token: '{{ csrf_token() }}'
                    };

                    try {
                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        const res = await response.json();

                        if (response.ok && res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (type === 'in') {
                                this.hasAttendanceToday = true;
                                this.attendanceData.time_in = res.attendance.time_in;
                                this.attendanceData.status = res.attendance.status;
                                this.attendanceData.lat_in = parseFloat(res.attendance.lat_in);
                                this.attendanceData.long_in = parseFloat(res.attendance.long_in);
                            } else {
                                this.attendanceData.time_out = res.attendance.time_out;
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan.',
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Gagal menghubungi server.',
                            confirmButtonColor: '#2563eb'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                formatTime(timeStr) {
                    if (!timeStr) return '-';
                    const parts = timeStr.split(':');
                    return parts[0] + ':' + parts[1] + (parts[2] ? ':' + parts[2] : '') + ' WIB';
                },

                async initPushNotification() {
                    if (!('Notification' in window) || !('serviceWorker' in navigator)) {
                        return;
                    }

                    try {
                        const reg = await navigator.serviceWorker.register('/sw.js?v=20260929_v3', { updateViaCache: 'none' });
                        try {
                            await reg.update();
                        } catch (e) {}
                        const existingSub = await reg.pushManager.getSubscription();

                        if (Notification.permission === 'granted') {
                            // Sinkronkan ke database server terlebih dahulu
                            const subscribed = await this.doSubscribePush(reg);
                            this.pushEnabled = !!subscribed;
                            return;
                        }

                        // OTOMATIS MINTA IZIN SEPERTI KAMERA & LOKASI
                        if (Notification.permission === 'default') {
                            try {
                                const perm = await Notification.requestPermission();
                                if (perm === 'granted') {
                                    const subscribed = await this.doSubscribePush(reg);
                                    this.pushEnabled = !!subscribed;
                                }
                            } catch (e) {
                                console.warn('Auto notification prompt blocked by browser:', e);
                            }

                            // Cadangan jika browser HP menuntut interaksi gesture sentuhan pertama
                            const triggerOnFirstTouch = async () => {
                                if (Notification.permission === 'default') {
                                    try {
                                        const p = await Notification.requestPermission();
                                        if (p === 'granted') {
                                            const r = await navigator.serviceWorker.ready;
                                            const subscribed = await this.doSubscribePush(r);
                                            this.pushEnabled = !!subscribed;
                                        }
                                    } catch (err) {}
                                }
                                window.removeEventListener('click', triggerOnFirstTouch);
                                window.removeEventListener('touchstart', triggerOnFirstTouch);
                            };
                            window.addEventListener('click', triggerOnFirstTouch, { once: true });
                            window.addEventListener('touchstart', triggerOnFirstTouch, { once: true });
                        }
                    } catch (err) {
                        console.warn('Push init error:', err);
                    }
                },

                async doSubscribePush(reg) {
                    try {
                        const keyRes = await fetch('{{ route("push.key") }}');
                        if (!keyRes.ok) {
                            console.error('Gagal mengambil VAPID Public Key dari server (HTTP ' + keyRes.status + ')');
                            return false;
                        }
                        const keyData = await keyRes.json();
                        if (!keyData.publicKey) {
                            console.error('VAPID_PUBLIC_KEY belum disetel di .env server production!');
                            return false;
                        }

                        const convertedKey = this.urlBase64ToUint8Array(keyData.publicKey);

                        let subscription = await reg.pushManager.getSubscription();
                        
                        // Coba buat subscription baru jika belum ada
                        if (!subscription) {
                            try {
                                subscription = await reg.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: convertedKey
                                });
                            } catch (subErr) {
                                console.warn('Gagal subscribe awal, coba unsubscribe subscription lama jika ada:', subErr);
                                const oldSub = await reg.pushManager.getSubscription();
                                if (oldSub) await oldSub.unsubscribe();
                                subscription = await reg.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: convertedKey
                                });
                            }
                        }

                        if (!subscription) {
                            console.error('Push subscription gagal dibuat oleh browser.');
                            return false;
                        }

                        const subJson = subscription.toJSON();
                        subJson.contentEncoding = (window.PushManager && PushManager.supportedContentEncodings)
                            ? PushManager.supportedContentEncodings[0]
                            : 'aes128gcm';

                        const response = await fetch('{{ route("push.subscribe") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(subJson)
                        });

                        const res = await response.json();
                        if (res.success) {
                            this.pushEnabled = true;
                            return true;
                        } else {
                            console.error('Server push.subscribe returned false:', res.message);
                            return false;
                        }
                    } catch (e) {
                        console.error('doSubscribePush error:', e);
                        return false;
                    }
                },

                urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                },

                async requestNotificationPermission() {
                    if (!('Notification' in window) || !('serviceWorker' in navigator)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Didukung di HTTP IP',
                            html: '<p class="text-xs">Browser HP memblokir izin notifikasi di koneksi HTTP IP. Buka via HTTPS atau aktifkan di <b>chrome://flags</b>.</p>',
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }

                    try {
                        const permission = await Notification.requestPermission();
                        if (permission === 'granted') {
                            const reg = await navigator.serviceWorker.ready;
                            const subscribed = await this.doSubscribePush(reg);
                            if (subscribed) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Notifikasi Aktif!',
                                    text: 'Sistem akan mengirimkan notifikasi pengingat 15 menit sebelum batas waktu absen masuk (08:15 WIB), bahkan saat browser ditutup.',
                                    confirmButtonColor: '#2563eb'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Izin Diberikan, Tapi Gagal Sinkron ke Server',
                                    html: '<p class="text-xs text-left">Browser sudah memberi izin, tetapi server belum berhasil menyimpan token perangkat Anda.<br><br><b>Kemungkinan penyebab di Production:</b><br>1. Kunci <code>VAPID_PUBLIC_KEY</code> & <code>VAPID_PRIVATE_KEY</code> belum diset di <code>.env</code> server.<br>2. Tabel <code>push_subscriptions</code> belum dimigrasi (<code>php artisan migrate</code>).<br>3. Config Laravel perlu di-clear (<code>php artisan config:clear</code>).</p>',
                                    confirmButtonColor: '#2563eb'
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Izin Notifikasi Belum Diberikan',
                                text: 'Silakan izinkan notifikasi pada pengaturan izin situs browser Anda.',
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengaktifkan',
                            text: err.message,
                            confirmButtonColor: '#2563eb'
                        });
                    }
                },

                async disableNotification() {
                    try {
                        const reg = await navigator.serviceWorker.ready;
                        const subscription = await reg.pushManager.getSubscription();
                        if (subscription) {
                            const endpoint = subscription.endpoint;
                            await subscription.unsubscribe();
                            await fetch('{{ route("push.unsubscribe") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ endpoint: endpoint })
                            });
                        }
                        this.pushEnabled = false;
                        Swal.fire({
                            icon: 'info',
                            title: 'Notifikasi Dinonaktifkan',
                            text: 'Pengingat otomatis telah dinonaktifkan untuk perangkat ini.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } catch (e) {
                        console.error('Unsubscribe error:', e);
                    }
                },

                async sendTestNotification() {
                    this.sendingTest = true;
                    try {
                        const response = await fetch('{{ route("push.test") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const res = await response.json();
                        if (res.success) {
                            // Tampilkan langsung pop-up modal di layar
                            this.showReminderPopup(
                                '🔔 Tes Pengingat Absensi!',
                                'Notifikasi uji coba berhasil dikirim! Pop-up ini otomatis muncul di layar dan di bilah notifikasi sistem HP / Laptop Anda.'
                            );
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    } catch (err) {
                        Swal.fire('Error', err.message, 'error');
                    } finally {
                        this.sendingTest = false;
                    }
                },

                lastPopupTime: 0,

                initServiceWorkerMessageListener() {
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.addEventListener('message', (event) => {
                            if (event.data && (event.data.type === 'PUSH_NOTIFICATION_RECEIVED' || event.data.type === 'PUSH_NOTIFICATION_CLICKED')) {
                                this.showReminderPopup(event.data.title, event.data.body);
                            }
                        });
                    }
                },

                checkUrlForNotificationPopup() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('push_popup') === '1') {
                        const title = urlParams.get('title') || '⏰ Pengingat Absensi!';
                        const body = urlParams.get('body') || 'Waktu absensi akan segera berakhir!';
                        
                        // Bersihkan parameter query pop-up dari URL
                        urlParams.delete('push_popup');
                        urlParams.delete('title');
                        urlParams.delete('body');
                        urlParams.delete('_t');
                        const newQuery = urlParams.toString() ? ('?' + urlParams.toString()) : '';
                        window.history.replaceState({}, document.title, window.location.pathname + newQuery);

                        setTimeout(() => {
                            this.showReminderPopup(title, body);
                        }, 400);
                    }
                },

                playNotificationSound() {
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContext) return;
                        const audioCtx = new AudioContext();
                        if (audioCtx.state === 'suspended') {
                            audioCtx.resume().catch(() => {});
                        }
                        
                        const now = audioCtx.currentTime;
                        // Nada 1 (D5)
                        const osc1 = audioCtx.createOscillator();
                        const gain1 = audioCtx.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(587.33, now);
                        gain1.gain.setValueAtTime(0.25, now);
                        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                        osc1.connect(gain1);
                        gain1.connect(audioCtx.destination);
                        osc1.start(now);
                        osc1.stop(now + 0.3);

                        // Nada 2 (A5)
                        const osc2 = audioCtx.createOscillator();
                        const gain2 = audioCtx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880, now + 0.15);
                        gain2.gain.setValueAtTime(0.3, now + 0.15);
                        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                        osc2.connect(gain2);
                        gain2.connect(audioCtx.destination);
                        osc2.start(now + 0.15);
                        osc2.stop(now + 0.55);
                    } catch(e) {}
                },

                showReminderPopup(title, body) {
                    const now = Date.now();
                    if (now - this.lastPopupTime < 3000) return;
                    this.lastPopupTime = now;

                    this.playNotificationSound();

                    if (typeof Swal === 'undefined') {
                        alert((title || 'Pengingat Absensi') + '\n\n' + (body || 'Waktu absensi akan segera berakhir!'));
                        return;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: title || '⏰ Pengingat Absensi!',
                        html: `
                            <div class="py-2 text-center">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">${body || 'Waktu absensi akan segera berakhir. Segera lakukan presensi!'}</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 text-xs font-bold">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                    <span>Waktu Sekarang: ${this.liveClockWib || ''} WIB</span>
                                </div>
                            </div>
                        `,
                        confirmButtonText: '📸 Absen Sekarang',
                        confirmButtonColor: '#2563eb',
                        showCancelButton: true,
                        cancelButtonText: 'Tutup',
                        cancelButtonColor: '#64748b',
                        focusConfirm: true,
                        backdrop: 'rgba(15, 23, 42, 0.75)',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl font-bold px-5 py-2.5 shadow-md',
                            cancelButton: 'rounded-xl font-semibold px-4 py-2.5'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const target = document.getElementById('webcam') || document.querySelector('button[type="submit"]');
                            if (target) {
                                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
