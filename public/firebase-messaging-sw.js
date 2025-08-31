// Firebase v8 scripts
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js");

// Initialize Firebase
firebase.initializeApp({
  apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
  authDomain: "nawloan-eff12.firebaseapp.com",
  projectId: "nawloan-eff12",
  storageBucket: "nawloan-eff12.appspot.com",
  messagingSenderId: "997400731253",
  appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
  measurementId: "G-8GEL2Y9LVZ"
});

// Get Firebase Messaging instance
const messaging = firebase.messaging();

// 🔔 Background handler → show notifications
messaging.setBackgroundMessageHandler(function(payload) {
  console.log("[FCM SW] Background message:", payload);

  const { title, body, icon } = payload.notification || {};
  return self.registration.showNotification(title || "New Notification", {
    body: body || "",
    icon: icon || "/favicon.ico"
  });
});
