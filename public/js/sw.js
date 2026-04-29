self.addEventListener('push', function (event) {
    const data = event.data.json();

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon || '/favicon.ico',
            data: data.data || {}
        })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data.url || '/';

    event.waitUntil(clients.openWindow(url));
});