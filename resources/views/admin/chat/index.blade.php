@extends('layouts.admin.app')
@section('title',' | ' .  __('site.customers_chats'))
@section('styles')

@endsection
@section('content')
<h2 class="section-title mb-4">@lang('site.customers_chats')</h2>
<div class="chat-wrapper">
    <div class="chat-sidebar">
        <div class="header">
            <a href="" class="sidebar-close">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                    <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" />
                </svg>
            </a>
            <div class="search-group search-icon">
                <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                <input type="search" name="search" id="search" placeholder="Search Message Or Name…">
            </div>
        </div>
        <ul class="inbox-list">
            @include('admin.chat.rooms')
        </ul>
    </div>
    <div class="inbox-details flex-column">
        <div class="inbox-header flex-space">
            @include('admin.chat.header')
        </div>
        <div id="messages" style="overflow-y: auto">
            <ul class="inbox-list" id="chat">
                <div id="msgs"></div>
            </ul>
        </div>
        <div class="inbox-footer">
            <form action="" class="d-flex">
                <div class="textarea-group">
                    <textarea name="" id="chat-text" data-url="{{route('admin.chat.store')}}" rows="4" placeholder="Write Your Message"></textarea>
                    <button type="submit" id="send_msg">
                        <img src="{{asset('assets/images/svgs/send-fill.svg')}}" alt="">
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $('#search').change(function (e) {
            e.preventDefault();
            var url = "{{ route('admin.chat.rooms')}}";
            $.ajax({
                "url": url,
                "type": "GET",
                "datatype": "json",
                data: {
                    'search': $('#search').val(),
                },
                success: function (data) {
                    // $('#breaks_div').html();
                    $('#rooms_dev').html(data);
                }
            });
        });
    });

    function selectRoom(room) {
        console.log(room)
        $('#room_title').text("{{__('site.order')}} # " + room.order_id);
        $('#room_id').text(room.id);
        $('#room_user_1').text(room.users[0].name);
        $('#room_user_11').text(room.users[0].name);
        $('#room_user_1_type').text(room.users[0].type);
        $('#room_user_2').text(room.users[1].name);
        $('#room_user_22').text(room.users[1].name);
        $('#room_user_2_type').text(room.users[1].type);
        let msgs = room.messages;
        let options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit', 
            hour12: true 
        };
        $('#chat').html('');
        $('#messages').css('display', 'block');
        msgs.forEach(function (msg) {
            console.log(msg.user.type);
            if (msg.user.type == "admin" || msg.user.type == 'emp' || msg.user.type == "superadministrator") {
                $('#chat').append(`
                    <li class="sent-message msg-admins">
                        <div class="message-inner">
                            <div class="flex-align-start">
                                <span class="message-content">
                                    <strong class="name">${msg.user.name}</strong>
                                    <span>${msg.body}</span>
                                </span>
                            </div>
                            <span class="time">${new Date(msg.created_at).toLocaleString('en-US', options)}</span>
                        </div>
                    </li>
                `);
            }
            else if ($('meta[name=user-id]').attr('content') == msg.user_id) {
                $('#chat').append(`
                    <li class="sent-message msg-send">
                        <div class="message-inner">
                            <div class="flex-align-start">
                                <span class="message-content">${msg.body}</span>
                            </div>
                            <span class="time">${new Date(msg.created_at).toLocaleString('en-US', options)}</span>
                        </div>
                    </li>
                `);
            }

            else {
                $('#chat').append(`
                    <li class="received-message msg-receive">
                        <div class="message-inner">
                            <div class="flex-align-start">
                                <span class="message-content">${msg.body}</span>
                            </div>
                            <span class="time">${new Date(msg.created_at).toLocaleString('en-US', options)}</span>
                        </div>
                    </li>
                `);
            }
            $("#chat").animate({
                    scrollTop: $("#chat")[0].scrollHeight,
                }, "fast"
            );
        })
    }
    $('#chat-text').keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            if ($(this).val() != '') {
                let body = $(this).val();
                let url = $(this).data('url');
                let data = {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'room_id': $('#room_id').text(),
                    body: body,
                    'user_id': $('meta[name=user-id]').attr('content'),
                }
                $(this).val('');
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data
                });
                // Get current time
                let currentTime = new Date();
                let localFormattedTime = currentTime.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true  // Ensure AM/PM format
                });

                $('#chat').animate({
                    scrollTop: $('#chat')[0].scrollHeight
                }, "slow");

                if ($('meta[name=user-type]').attr('content') == "admin" || $('meta[name=user-type]').attr('content') == 'emp' || $('meta[name=user-type]').attr('content') == "superadministrator") {
                    $('#chat').append(`
                        <li class="sent-message msg-admins">
                            <div class="message-inner">
                                <div class="flex-align-start">
                                    <span class="message-content">
                                        <strong class="name">${$('meta[name=user-name]').attr('content')}</strong>
                                        <span>${body}</span>
                                    </span>
                                </div>
                                <span class="time">${localFormattedTime}</span>
                            </div>
                        </li>
                    `);
                }
                else {
                    $('#chat').append(`
                        <li class="sent-message msg-send">
                            <div class="message-inner">
                                <div class="flex-align-start">
                                    <span class="message-content">${body}</span>
                                </div>
                                <span class="time">${localFormattedTime}</span>
                            </div>
                        </li>
                    `);
                }
            }
        }
    });
    $('#send_msg').on('click', function (e) {
        e.preventDefault();
        if ($('#chat-text').val() != '') {
            let body = $('#chat-text').val();
            let url = $('#chat-text').data('url');
            let data = {
                '_token': $('meta[name=csrf-token]').attr('content'),
                'room_id': $('#room_id').text(),
                body: body,
                'user_id': $('meta[name=user-id]').attr('content'),
            }
            $('#chat-text').val('');
            $.ajax({
                url: url,
                method: 'POST',
                data: data
            });
            $('#chat').animate({
                scrollTop: $('#chat')[0].scrollHeight
            }, "slow");
            if ($('meta[name=user-type]').attr('content') == "admin" || $('meta[name=user-type]').attr('content') == 'emp' || $('meta[name=user-type]').attr('content') == "superadministrator") {
                $('#chat').append(`
                    <li class="sent-message msg-admins">
                        <div class="message-inner">
                            <div class="flex-align-start">
                                <span class="message-content">
                                    <strong class="name">${$('meta[name=user-name]').attr('content')}</strong>
                                    <span>${body}</span>
                                </span>
                            </div>
                            <span class="time">10:15 am</span>
                        </div>
                    </li>
                `);
            }
            else {
                $('#chat').append(`
                    <li class="sent-message msg-send">
                        <div class="message-inner">
                            <div class="flex-align-start">
                                <span class="message-content">${body}</span>
                            </div>
                            <span class="time">10:15 am</span>
                        </div>
                    </li>
                `);
            }
        }
    });
</script>
@endsection
