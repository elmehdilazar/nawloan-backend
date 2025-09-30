/* global importScripts, firebase */
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging-compat.js');

// IMPORTANT: these must match your web config:
const firebaseConfig = {
  apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
  authDomain: "nawloan-eff12.firebaseapp.com",
  databaseURL: "https://nawloan-eff12-default-rtdb.firebaseio.com",
  projectId: "nawloan-eff12",
  storageBucket: "nawloan-eff12.firebasestorage.app",
  messagingSenderId: "997400731253",
  appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
  measurementId: "G-8GEL2Y9LVZ"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// Background messages (when page is closed or hidden)
messaging.onBackgroundMessage((payload) => {
  // Customize notification
  const title = (payload.notification && payload.notification.title) || 'New message';
  const body  = (payload.notification && payload.notification.body)  || '';
  const options = {
    body,
    data: payload.data || {},
  };
  var audio = new Audio('audio_file.wav');
audio.play();
alert("good");
  self.registration.showNotification(title, options);
});
