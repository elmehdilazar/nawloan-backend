/* FCM Service Worker (v10 compat) */
importScripts('https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js');

firebase.initializeApp({
   apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
       authDomain: "nawloan-eff12.firebaseapp.com",
       databaseURL: "https://nawloan-eff12-default-rtdb.firebaseio.com",
       projectId: "nawloan-eff12",
       storageBucket: "nawloan-eff12.appspot.com",
       messagingSenderId: "997400731253",
       appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
       measurementId: "G-8GEL2Y9LVZ"
});

const messaging = firebase.messaging();

// Show a system notification when the page is in background
messaging.onBackgroundMessage((payload) => {
    const {
        title,
        body
    } = payload.notification || {};
    self.registration.showNotification(title || 'New notification', {
        body: body || '',
        data: payload.data || {},
    });

    // Notify open tabs to play sound & show toast
    self.clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    }).then((clients) => {
        clients.forEach((c) => c.postMessage({
            __fcm: true,
            payload
        }));
    });
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil((async () => {
        const clients = await self.clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        });
        if (clients.length) return clients[0].focus();
        return self.clients.openWindow('/'); // admin home
    })());
});
