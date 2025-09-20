/* global importScripts, firebase */
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

// IMPORTANT: these must match your web config:
const firebaseConfig = {
apiKey: "AIzaSyBKqTK4HMT4oH-LzuOSjCtRaAGqAPa61EI",
  authDomain: "fcm-project-99b61.firebaseapp.com",
  projectId: "fcm-project-99b61",
 appId: "1:816565644651:web:141613f5d9e6d9dacf302e",
    messagingSenderId: "816565644651",
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
  self.registration.showNotification(title, options);
});
