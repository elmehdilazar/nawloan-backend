"use strict";

require("./bootstrap");

var _laravelEcho = _interopRequireDefault(require("laravel-echo"));

var _socket = _interopRequireDefault(require("socket.io-client"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

window.io = _socket["default"]; //from 'socket.io-client' ;

window.Echo = new _laravelEcho["default"]({
  broadcaster: 'socket.io',
  host: window.location.hostname + ':6001' // host: 'http://localhost:6001'

});
/* Echo.join(`chat`)
    .here((users) => {
        users.forEach(function(user){
            $('#online-users').append(`<li class="list-group-iten">${user.name}</li>`);
        })
        console.log(users);
    }).joining((user)=>{
        console.log(user);
    }).leaving((user)=>{
        console.log(user);
    })
    .error((error) => {
        console.error(error);
    });
 */

var usersLength = 0;
/* window.Echo.join(`chat`)
    .here((users) => {
        console.log(users);
        let userId = $("meta[name=user-id]").attr("content");
        usersLength = users.lenght;
        if (usersLength > 1) {
            $("#no-online").css("display", "none");
            $("#online").css("display", "block");
        }
        users.forEach(function (user) {
            if (user.id == userId) {
                return;
            }
            $("#online-users").append(
                `<li class="list-group-item" id="user-${user.id}"><div class="d-flex justify-content-betweeen">${user.name}<span class="icon icon-circle text-success mx-2">online</span></div></li>`
            );
        });
    })
    .joining((user) => {
        usersLength++;
        if (usersLength > 1) {
            $("#no-online").css("display", "none");
            $("#online").css("display", "block");
        }
        $("#online-users").append(
            `<li class="list-group-item"><div class="d-flex justify-content-betweeen">${user.name}<span class="icon icon-circle text-success mx-2">online</span></div></li>`
        );
    })
    .leaving((user) => {
        usersLength--;
        if (usersLength == 1) {
            $("#online").css("display", "block");
            $("#online").css("display", "none");
        }
        $("#user-" + user.id).remove();
    })
    .error((error) => {
        console.error(error);
    });
`+$('#room_id').val()
 */

window.Echo.channel("laravel_database_chat").listen(".chat", function (e) {
  var src = "http://localhost:8000/audio/beep.mp3";
  var audio = new Audio(src);
  audio.play();

  if (e.message.room_id == $("#room_id").text()) {
    $("#chat").animate({
      scrollTop: $("#chat")[0].scrollHeight
    }, "slow");

    if (msg.user.type == "admin" || msg.user.type == 'emp' || msg.user.type == "superadministrator") {
      $('#chat').append("\n        <div class=\"mt-2  text-white p-0 rounded msg-admins\" style=\"display:grid\">\n            <span class=\"px-3 pt-1\"\n            style=\"text-align-last:start;width: 100%;color:#001a4e !important;\"> ".concat(msg.user.name, "</span>\n        <p class=\"px-3 pb-1\"\n            style=\"text-align-last:start;width: 100%;\">").concat(msg.body, "</p>\n    </div>\n    <div class=\"clearfix\"></div>"));
    } else {
      $("#chat").append("<div class=\"mt-2 text-white p-0 rounded float-left msg-receive\"\n            >\n            <p class=\"p-2 mb-0\"\n        style=\"text-align-last:end;width: 100%;\">".concat(e.message.body, "</p>\n        </div>\n        <div class=\"clearfix\"></div>"));
    }
  } else {
    $("room-bell-".concat(e.message.room_id)).addClass("text-success");
  }
}); // window.Echo.channel(`laravel_database_online`).listen(".examplee", (e) => {
//     console.log(e);
// });
//# sourceMappingURL=app.dev.js.map
