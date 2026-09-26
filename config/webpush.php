<?php

return [
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@absensi.local'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],
    // Batas waktu toleransi masuk (default 08:30 WIB) & pengingat dikirim 15 menit sebelumnya (08:15 WIB)
    'reminder' => [
        'minutes_before' => 15,
        'clock_in_deadline' => env('ATTENDANCE_DEADLINE', '08:30:00'),
    ],
];
