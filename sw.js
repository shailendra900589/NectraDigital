const CACHE_NAME = 'nectra-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('push', (event) => {
    let data = { title: 'Nectra Digital', body: 'New update available!', icon: '/nectradigital_final/assets/images/logo.png', url: '/nectradigital_final/' };
    
    if (event.data) {
        try { data = event.data.json(); } catch(e) { data.body = event.data.text(); }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/nectradigital_final/assets/images/logo.png',
        badge: '/nectradigital_final/assets/favicon_io/favicon-32x32.png',
        image: data.image || null,
        vibrate: [100, 50, 100],
        data: { url: data.url || '/nectradigital_final/' },
        actions: [
            { action: 'open', title: 'Read Now' },
            { action: 'close', title: 'Dismiss' }
        ]
    };

    event.waitUntil(self.registration.showNotification(data.title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'close') return;
    
    const url = event.notification.data.url || '/nectradigital_final/';
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes('nectradigital') && 'focus' in client) return client.focus();
            }
            return clients.openWindow(url);
        })
    );
});
