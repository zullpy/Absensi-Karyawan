<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private string $token;
    private string $endpoint = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Kirim OTP ke nomor WhatsApp via Fonnte.
     */
    public function sendOtp(string $no_hp, string $otp): bool
    {
        $message = "🔐 *HADIRin - Kode OTP Reset Password*\n\n"
                 . "Kode OTP Anda: *{$otp}*\n\n"
                 . "Kode ini berlaku selama *10 menit*.\n"
                 . "Jangan bagikan kode ini kepada siapapun.\n\n"
                 . "_Jika Anda tidak meminta reset password, abaikan pesan ini._";

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $this->token])
                ->asForm()
                ->post($this->endpoint, [
                    'target'      => $no_hp,
                    'message'     => $message,
                    'countryCode' => '62',
                ]);

            $body = $response->json();

            if ($response->successful() && isset($body['status']) && $body['status'] === true) {
                return true;
            }

            Log::warning('Fonnte send failed', ['response' => $body, 'no_hp' => $no_hp]);
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte exception', ['error' => $e->getMessage(), 'no_hp' => $no_hp]);
            return false;
        }
    }
}
