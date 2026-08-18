<x-guest-layout>
    <div class="card-header">
        <h1>Verifikasi OTP</h1>
        <p>Masukkan kode 6 digit yang telah dikirim ke WhatsApp Anda.</p>
    </div>

    @if (session('status'))
        <div class="session-alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.otp.verify.post') }}" id="otpForm">
        @csrf

        <div class="form-group">
            <label style="text-align:center; display:block; margin-bottom:12px;">Kode OTP</label>

            {{-- 6 kotak OTP --}}
            <div id="otpBoxes" style="display:flex; gap:10px; justify-content:center; margin-bottom:8px;">
                @for ($i = 0; $i < 6; $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        class="otp-box"
                        id="otp_{{ $i }}"
                        autocomplete="off"
                        style="
                            width: 48px; height: 56px;
                            text-align: center;
                            font-size: 22px; font-weight: 700;
                            background: var(--bg-input);
                            border: 2px solid var(--border-input);
                            border-radius: 12px;
                            color: var(--text-primary);
                            outline: none;
                            transition: border-color 0.2s, box-shadow 0.2s;
                            font-family: 'Inter', sans-serif;
                        "
                    >
                @endfor
            </div>

            {{-- Hidden input yang dikirim ke server --}}
            <input type="hidden" name="otp" id="otpHidden">

            @error('otp')
                <p class="error-text" style="justify-content:center;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;flex-shrink:0"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Timer kedaluwarsa --}}
        <p style="text-align:center; font-size:13px; color:var(--text-muted); margin-bottom:20px;">
            Kode kedaluwarsa dalam <span id="countdown" style="color:var(--primary); font-weight:600;">10:00</span>
        </p>

        <button type="submit" class="btn-login" id="btnVerify">
            <span class="btn-login-inner">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Verifikasi
            </span>
        </button>

        <div style="text-align:center; margin-top:16px;">
            <a href="{{ route('password.request') }}" class="forgot-link">← Kirim ulang OTP</a>
        </div>
    </form>

    <script>
    // ── OTP Box Navigation ─────────────────────────────────────────
    (function () {
        var boxes = document.querySelectorAll('.otp-box');
        var hidden = document.getElementById('otpHidden');
        var form   = document.getElementById('otpForm');

        boxes.forEach(function (box, idx) {
            box.addEventListener('focus', function () {
                box.style.borderColor = 'var(--border-focus)';
                box.style.boxShadow   = 'var(--ring-focus)';
                box.select();
            });
            box.addEventListener('blur', function () {
                box.style.borderColor = 'var(--border-input)';
                box.style.boxShadow   = 'none';
            });
            box.addEventListener('input', function () {
                // Hanya angka
                box.value = box.value.replace(/\D/, '');
                if (box.value && idx < boxes.length - 1) {
                    boxes[idx + 1].focus();
                }
                syncHidden();
            });
            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    boxes[idx - 1].focus();
                }
            });
            box.addEventListener('paste', function (e) {
                e.preventDefault();
                var data = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                data.split('').forEach(function (ch, i) {
                    if (boxes[i]) boxes[i].value = ch;
                });
                if (boxes[Math.min(data.length, 5)]) boxes[Math.min(data.length, 5)].focus();
                syncHidden();
            });
        });

        function syncHidden() {
            hidden.value = Array.from(boxes).map(function(b){ return b.value; }).join('');
        }

        form.addEventListener('submit', function () { syncHidden(); });
        boxes[0].focus();
    })();

    // ── Countdown Timer ────────────────────────────────────────────
    (function () {
        var total   = 10 * 60; // 10 menit
        var display = document.getElementById('countdown');
        var timer   = setInterval(function () {
            total--;
            if (total <= 0) {
                clearInterval(timer);
                display.textContent = '00:00';
                display.style.color = '#ef4444';
                return;
            }
            var m = String(Math.floor(total / 60)).padStart(2, '0');
            var s = String(total % 60).padStart(2, '0');
            display.textContent = m + ':' + s;
        }, 1000);
    })();
    </script>
</x-guest-layout>
