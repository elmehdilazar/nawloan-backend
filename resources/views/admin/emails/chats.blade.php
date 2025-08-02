@extends('layouts.admin.app')
@section('styles')

@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="users-online">
                <button type="button" class="btn btn-primary">
                    Users: <span class="badge badge-light" id="userOnline"></span>
                </button>
            </div>
            <div class="online-users">
                <div class="d-flex flex-column mb-3 available-users">
                    @foreach ($data['listOfUsers'] as $user)
                    <div class="p-2"><a href="/users/{{ $user->id }}">{{ $user->name }}</a></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="users-online">
                <button type="button" class="btn btn-primary">
                    Your friends
                </button>
            </div>
            <div class="user-rooms">
                <div class="d-flex flex-column mb-3 available-rooms">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
     $('#btnSend').click(function(){
        $.ajaxSetup(ajaxSetupHeader);
        var messageContent = $("#msgContent").val();
        $.ajax({
            url: "/messages/chat",
            method: "POST",
            data: { 
                message : $("#msgContent").val(),
                sender_id: $("#senderId").val(),
                receiver_id: $("#receiverId").val(),
                device_token: $("#deviceToken").val(),
            }
        }).done(function( msg ) {
            console.log(msg);
            appendMessage(msg['message']);
            scrollToButtom('.messages');
            $("#msgContent").val("");
        }).fail(function( jqXHR, textStatus ) {
            console.log( "sendChatMessage FAILED " + textStatus );
        });
    });
    function getFriends() {
    $.ajaxSetup(ajaxSetupHeader);
    $.ajax({
        url: "/status",
        method: "GET",
        async: false,
    }).done(function( res ) {
        friends = res;
        listAvailableFriends(friends);
    }).fail(function( jqXHR, textStatus ) {
        console.log( "getFriends is Failed" + textStatus );
    });    
}
function listAvailableFriends(friends) {
    let item = '';
    for (const index in friends) {
        let uid = friends[index].firebase_uid;
        let status = "off-circle";
        item += `<div class="p-2"><div class="${status}" id="${uid}"></div><a href="/users/${friends[index].id}">${friends[index].name}</a></div>`;
        updateUserRealtimeState(uid);
    }
    $(".available-rooms").html(item);
}
function updateUserRealtimeState (uid) {
    let ref = 'status/' + uid;
    database.ref(ref).on('value', snap => {
        let userRealtimeState = snap.val();
        if (userRealtimeState.state == 'online') {
            $(`#${uid}`).addClass('on-circle');
            $(`#${uid}`).removeClass('off-circle');
        } else {
            $(`#${uid}`).removeClass('on-circle');
            $(`#${uid}`).addClass('off-circle');
        }
    });
}
    </script>
@endsection