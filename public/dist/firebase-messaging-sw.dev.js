"use strict";

importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
firebase.initializeApp({
  apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
  authDomain: "nawloan-eff12.firebaseapp.com",
  projectId: "nawloan-eff12",
  storageBucket: "nawloan-eff12.appspot.com",
  messagingSenderId: "997400731253",
  appId: "1:997400731253:web:d0ae522e19b8fce924a23c"
});
var messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (_ref) {
  var _ref$data = _ref.data,
      title = _ref$data.title,
      body = _ref$data.body,
      icon = _ref$data.icon;
  return self.registration.showNotification(title, {
    body: body,
    icon: icon
  });
});
//# sourceMappingURL=firebase-messaging-sw.dev.js.map
