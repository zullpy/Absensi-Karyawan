// Service Worker untuk Web Push Notification Aplikasi Absensi
const SW_VERSION = '2026-09-29-v3';

self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});

// Tangkap Push Event dari Server (bahkan ketika browser/tab sedang ditutup)
self.addEventListener('push', function(event) {
    let data = {
        title: '⏰ Peringatan Absensi',
        body: 'Waktu absensi akan segera berakhir!',
        url: '/'
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const title = data.title || '⏰ Peringatan Absensi';
    const body = data.body || 'Waktu absensi akan segera berakhir!';
    const rawUrl = data.url || '/';

    // Pastikan URL selalu absolut agar aman di semua jenis browser HP
    const targetUrl = new URL(rawUrl, self.location.origin).href;

    const options = {
        body: body,
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
        tag: data.tag || ('absensi-reminder-' + Date.now()),
        renotify: true,
        requireInteraction: true,
        vibrate: data.vibrate || [300, 100, 300, 100, 300],
        data: {
            url: targetUrl,
            title: title,
            body: body
        },
        actions: [
            { action: 'open', title: '📸 Buka Absensi' },
            { action: 'dismiss', title: 'Tutup' }
        ]
    };

    const notificationPromise = self.registration.showNotification(title, options).catch(function(err) {
        console.error('showNotification options error, falling back:', err);
        return self.registration.showNotification(title, {
            body: body,
            icon: '/favicon.ico'
        });
    });

    // Kirim pesan ke tab web yang mungkin sedang terbuka di HP/desktop agar memunculkan Pop-up seketika
    const broadcastPromise = self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
        clientList.forEach(function(client) {
            client.postMessage({
                type: 'PUSH_NOTIFICATION_RECEIVED',
                title: title,
                body: body,
                url: targetUrl
            });
        });
    }).catch(function() {});

    event.waitUntil(Promise.all([notificationPromise, broadcastPromise]));
});

// Aksi ketika notifikasi diklik oleh pengguna (baik klik isi notifikasi atau tombol "Buka Absensi")
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // Jika pengguna menekan tombol dismiss/tutup
    if (event.action === 'dismiss') {
        return;
    }

    const notifData = event.notification.data || {};
    const rawUrl = notifData.url || '/';
    const title = notifData.title || event.notification.title || '⏰ Peringatan Absensi';
    const body = notifData.body || event.notification.body || '';

    // WAJIB URL ABSOLUT untuk Chrome Android: clients.openWindow() akan melempar TypeError jika relative!
    const openUrl = new URL(rawUrl, self.location.origin);
    openUrl.searchParams.set('push_popup', '1');
    openUrl.searchParams.set('title', title);
    openUrl.searchParams.set('body', body);
    openUrl.searchParams.set('_t', Date.now().toString());

    const finalTargetUrl = openUrl.href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Jika ada tab yang sudah terbuka di HP/browser, fokuskan dan navigasi ke URL pop-up
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if ('focus' in client) {
                    client.postMessage({
                        type: 'PUSH_NOTIFICATION_CLICKED',
                        title: title,
                        body: body,
                        url: finalTargetUrl
                    });

                    if ('navigate' in client) {
                        return client.navigate(finalTargetUrl).then(function(navClient) {
                            return (navClient || client).focus();
                        }).catch(function() {
                            return client.focus();
                        });
                    }
                    return client.focus();
                }
            }

            // Jika tab belum terbuka, buka window baru (ini PASTI membawa browser HP ke depan)
            if (clients.openWindow) {
                return clients.openWindow(finalTargetUrl);
            }
        }).catch(function(err) {
            console.error('Error handling notificationclick:', err);
            if (clients.openWindow) {
                return clients.openWindow(finalTargetUrl);
            }
        })
    );
});
