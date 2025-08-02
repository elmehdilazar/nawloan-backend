importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
firebase.initializeApp({
    apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
    authDomain: "nawloan-eff12.firebaseapp.com",
    projectId: "nawloan-eff12",
    storageBucket: "nawloan-eff12.appspot.com",
    messagingSenderId: "997400731253",
    appId: "1:997400731253:web:d0ae522e19b8fce924a23c"
});*/ const firebaseConfig = {
    apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
    authDomain: "nawloan-eff12.firebaseapp.com",
    databaseURL: "https://nawloan-eff12-default-rtdb.firebaseio.com",
    projectId: "nawloan-eff12",
    storageBucket: "nawloan-eff12.appspot.com",
    messagingSenderId: "997400731253",
    appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
    measurementId: "G-8GEL2Y9LVZ"
  };
 function initFirebaseMessagingRegistration() {
        messaging.requestPermission().then(function () {
            return messaging.getToken();
        }).then(function(token) {
            axios.post("{{ route('admin.fcmToken') }}",{
                _method:"PATCH",
                token
            }).then(({data})=>{
                $('#fcmtoken').val(token);
                console.log('success',data)
            }).catch(({response:{data}})=>{
                console.error('errors',data)
            })
        }).catch(function (err) {
            console.log(`Token Error :: ${err}`);
        });
    }

    initFirebaseMessagingRegistration();
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    return self.registration.showNotification(title,{body,icon});
});