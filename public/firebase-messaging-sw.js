/* firebase-messaging-sw.js */
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

// Same config as index.html
firebase.initializeApp({
  apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
  authDomain: "nawloan-eff12.firebaseapp.com",
  projectId: "nawloan-eff12",
  messagingSenderId: "997400731253",
  appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
});

const swMessaging = firebase.messaging();

// Background messages only:
swMessaging.onBackgroundMessage((payload) => {
  // Try notification first, then data
  alert('Background message received. ' + JSON.stringify(payload));
  const title = (payload.notification && payload.notification.title)
             || (payload.data && payload.data.title)
             || 'New message';
  const body  = (payload.notification && payload.notification.body)
             || (payload.data && payload.data.body)
             || '';

  self.registration.showNotification(title, {
    body,
    data: payload.data || {}
  });
});