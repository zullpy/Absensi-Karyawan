// Service Worker untuk Web Push Notification Aplikasi Absensi
self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});

// Tangkap Push Event dari Server (bahkan ketika browser/tab sedang ditutup)
self.addEventListener('push', function(event) {
    let data = {
        title: 'Peringatan Absensi',
        body: 'Waktu absensi akan segera berakhir!',
        url: '/dashboard'
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const title = data.title || 'Pemberitahuan Absensi';
    const options = {
        body: data.body,
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
        tag: data.tag || ('absensi-reminder-' + Date.now()),
        renotify: true,
        requireInteraction: true,
        vibrate: data.vibrate || [200, 100, 200, 100, 300],
        data: {
            url: data.url || '/dashboard'
        },
        actions: [
            { action: 'open', title: 'Buka Absensi' },
            { action: 'dismiss', title: 'Tutup' }
        ]
    };

    const notificationPromise = self.registration.showNotification(title, options).catch(function(err) {
        console.error('showNotification options error, falling back:', err);
        return self.registration.showNotification(title, {
            body: data.body || 'Peringatan absensi karyawan.',
            icon: '/favicon.ico'
        });
    });

    // Kirim pesan ke tab web yang sedang terbuka agar memunculkan Pop-up langsung di layar
    const broadcastPromise = self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
        clientList.forEach(function(client) {
            client.postMessage({
                type: 'PUSH_NOTIFICATION_RECEIVED',
                title: title,
                body: data.body,
                url: data.url
            });
        });
    });

    event.waitUntil(Promise.all([notificationPromise, broadcastPromise]));
});

// Aksi ketika notifikasi diklik oleh pengguna
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    const baseTargetUrl = event.notification.data && event.notification.data.url 
        ? event.notification.data.url 
        : '/dashboard';

    const sep = baseTargetUrl.includes('?') ? '&' : '?';
    const popupTargetUrl = baseTargetUrl + sep + 'push_popup=1&title=' + encodeURIComponent(event.notification.title || '') + '&body=' + encodeURIComponent(event.notification.body || '');

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Jika ada tab yang sudah terbuka, kirim event pop-up dan fokuskan tab tersebut
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if ('focus' in client) {
                    client.postMessage({
                        type: 'PUSH_NOTIFICATION_CLICKED',
                        title: event.notification.title,
                        body: event.notification.body
                    });
                    client.navigate(popupTargetUrl);
                    return client.focus();
                }
            }
            // Jika belum ada tab yang terbuka, buka jendela baru dengan query pop-up
            if (clients.openWindow) {
                return clients.openWindow(popupTargetUrl);
            }
        })
    );
});
