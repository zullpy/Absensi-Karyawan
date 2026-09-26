<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $subject = config('webpush.vapid.subject');
        $publicKey = config('webpush.vapid.public_key');
        $privateKey = config('webpush.vapid.private_key');

        if ($publicKey && $privateKey) {
            $auth = [
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ]
            ];

            $defaultOptions = [
                'TTL' => 3600,
                'urgency' => 'high',
            ];

            $this->webPush = new WebPush($auth, $defaultOptions);
            $this->webPush->setReuseVAPIDHeaders(true);
            $this->webPush->setAutomaticPadding(false);
        }
    }

    /**
     * Send notification payload to all subscriptions belonging to a user.
     */
    public function sendToUser(User $user, string $title, string $body, array $extra = []): array
    {
        if (!$this->webPush) {
            return [];
        }

        $subscriptions = $user->pushSubscriptions;
        $results = [];

        foreach ($subscriptions as $sub) {
            $results[] = $this->sendToSubscription($sub, $title, $body, $extra);
        }

        return $results;
    }

    /**
     * Send notification payload to a single subscription.
     */
    public function sendToSubscription(PushSubscription $sub, string $title, string $body, array $extra = []): bool
    {
        if (!$this->webPush) {
            return false;
        }

        $payload = json_encode(array_merge([
            'title' => $title,
            'body' => $body,
            'icon' => '/favicon.ico',
            'badge' => '/favicon.ico',
            'url' => '/dashboard',
            'timestamp' => now()->timestamp,
        ], $extra));

        try {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
                'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
            ]);

            $report = $this->webPush->sendOneNotification($subscription, $payload, [
                'urgency' => 'high',
                'TTL' => 3600,
            ]);

            // If subscription is expired or gone (404, 410), delete from database
            if (!$report->isSuccess()) {
                $code = $report->getResponse()?->getStatusCode();
                if (in_array($code, [404, 410])) {
                    $sub->delete();
                }
                Log::warning("WebPush failed: " . $report->getReason());
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("WebPush exception for subscription {$sub->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send attendance reminder notification.
     */
    public function sendAttendanceReminder(User $user, int $minutesLeft = 15, string $deadline = '08:30 WIB'): array
    {
        $title = "⏰ Peringatan Absen Masuk ({$minutesLeft} Menit Lagi)";
        $body = "Halo {$user->username}, batas waktu absen masuk adalah pukul {$deadline}. Segera lakukan presensi sekarang agar tidak terlambat!";

        return $this->sendToUser($user, $title, $body, [
            'tag' => 'attendance-reminder-' . now()->toDateString(),
            'renotify' => true,
            'vibrate' => [200, 100, 200, 100, 300],
            'url' => route('dashboard'),
        ]);
    }
}
