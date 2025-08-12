/* FCM SW (v10 compat) */
importScripts('https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
  authDomain: "nawloan-eff12.firebaseapp.com",
  projectId: "nawloan-eff12",
  messagingSenderId: "997400731253",
  appId: "1:997400731253:web:d0ae522e19b8fce924a23c"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  // This fires when the tab is not focused or closed
  const { title, body } = payload.notification || {};
  self.registration.showNotification(title || 'New notification', {
    body: body || '',
    data: payload.data || {}
  });

  // forward to any open tabs (for sound + toast)
  self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
    clients.forEach(c => c.postMessage({ __fcm: true, payload }));
  });
});
