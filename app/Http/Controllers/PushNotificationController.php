<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    /**
     * Get VAPID Public Key for subscription.
     */
    public function getPublicKey()
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    }

    /**
     * Store or update user Web Push subscription.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();
        $endpoint = $request->endpoint;
        $endpointHash = hash('sha256', $endpoint);

        $subscription = PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'user_id' => $user->id,
                'endpoint' => $endpoint,
                'public_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'content_encoding' => $request->input('contentEncoding', 'aes128gcm'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi pengingat absensi berhasil diaktifkan.',
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Remove user Web Push subscription.
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $endpointHash = hash('sha256', $request->endpoint);
        PushSubscription::where('endpoint_hash', $endpointHash)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi pengingat absensi dinonaktifkan.',
        ]);
    }

    /**
     * Send instant test notification to current user.
     */
    public function sendTest(WebPushService $webPushService)
    {
        $user = Auth::user();

        if ($user->pushSubscriptions()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat Anda belum terdaftar untuk menerima notifikasi. Silakan klik tombol "Aktifkan Notifikasi" terlebih dahulu.',
            ], 422);
        }

        $results = $webPushService->sendToUser(
            $user,
            '🔔 Tes Pengingat Absensi Berhasil!',
            'Notifikasi ini akan otomatis muncul 15 menit sebelum batas absen, bahkan saat website / browser ditutup.',
            [
                'tag' => 'test-notification-' . time(),
                'url' => route('dashboard'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi uji coba telah dikirim ke perangkat Anda.',
            'sent_count' => count(array_filter($results)),
        ]);
    }
}
