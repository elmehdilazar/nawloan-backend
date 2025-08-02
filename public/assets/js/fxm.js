importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-auth.js');

function loginThenUpdateFirebaseToken(idTarget) {
    messaging.requestPermission()
    .then(function () {
        return messaging.getToken()
    })
    .then(function(token) {
        console.log(token);
        $(idTarget).val(token);
    }).catch(function (err) {
        console.log('GET DEVICE TOKEN ERROR: '+ err);
    });
}

firebase.initializeApp({
    apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
    authDomain: "nawloan-eff12.firebaseapp.com",
    projectId: "nawloan-eff12",
    storageBucket: "nawloan-eff12.appspot.com",
    messagingSenderId: "997400731253",
    appId: "1:997400731253:web:d0ae522e19b8fce924a23c"

var friends;

 

// Initialize Firebase

firebase.initializeApp(firebaseConfig);

 

const messaging = firebase.messaging();

const database = firebase.database();

const auth = firebase.auth();

 

messaging.onMessage(function(payload) {

    console.log("onMessage: " + payload.notification.body);

    let notificationBody = JSON.parse(payload.notification.body);

    const noteTitle = payload.notification.title;

    const noteOptions = {

        body: notificationBody.notifyContent,

        icon: payload.notification.icon,

    };

    new Notification(noteTitle, noteOptions);

    appendMessage(notificationBody.msgContent);

    scrollToButtom('.messages');

}, e => {

    console.log(e);

});
