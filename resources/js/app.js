import './bootstrap';
import Echo from "laravel-echo";
import Socket from 'socket.io-client' ;
window.io=Socket;//from 'socket.io-client' ;
window.Echo=new Echo({
    broadcaster:'socket.io',
     host:window.location.hostname + ':6001'
   // host: 'http://localhost:6001'
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
    let usersLength=0;
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
window.Echo.channel(`laravel_database_chat`).listen(".chat", (e) => {
    let src = "http://localhost:8000/audio/beep.mp3";
    const audio = new Audio(src);
    audio.play();
    if (e.message.room_id == $("#room_id").text()) {
        $("#chat").animate(
            {
                scrollTop: $("#chat")[0].scrollHeight,
            },
            "slow"
        );
        if(msg.user.type == "admin" || msg.user.type =='emp' || msg.user.type =="superadministrator")
       {
         $('#chat').append(`
        <div class="mt-2  text-white p-0 rounded msg-admins" style="display:grid">
            <span class="px-3 pt-1"
            style="text-align-last:start;width: 100%;color:#001a4e !important;"> ${msg.user.name}</span>
        <p class="px-3 pb-1"
            style="text-align-last:start;width: 100%;">${msg.body}</p>
    </div>
    <div class="clearfix"></div>`);
       }
       else{
        $("#chat").append(
            `<div class="mt-2 text-white p-0 rounded float-left msg-receive"
            >
            <p class="p-2 mb-0"
        style="text-align-last:end;width: 100%;">${e.message.body}</p>
        </div>
        <div class="clearfix"></div>`
        );}
    } else {
        $(`room-bell-${e.message.room_id}`).addClass("text-success");
    }
});
// window.Echo.channel(`laravel_database_online`).listen(".examplee", (e) => {
//     console.log(e);
// });
