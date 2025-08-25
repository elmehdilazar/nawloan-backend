
importScripts("https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js");

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log("[SW] Background message:", payload);

  const { title, body, icon } = payload.notification;

  self.registration.showNotification(title, {
    body,
    icon: icon || "/favicon.ico",
  });

  // Forward message to open tabs
  self.clients.matchAll({ type: "window", includeUncontrolled: true }).then(clients => {
    clients.forEach(client => client.postMessage({ __fcm: true, payload }));
  });
});                                        
