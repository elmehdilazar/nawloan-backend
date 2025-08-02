{{--<div class="d-flex justify-content-between" style="border-bottom:1px solid #ccc;">--}}
{{--    <div class="">--}}
{{--        <h4 id="room_user_1" style="display:none;"></h4>--}}
{{--             <div class="d-flex">--}}
{{--                <img src="{{asset('uploads/users/default.png')}}"--}}
{{--                style="height: 50px">--}}
{{--                <div style="display:grid">--}}
{{--                    <span style="padding:12px;padding-bottom: 0px;" id="room_user_11"></span>--}}
{{--                    <span class="text-muted" style="text-align-last: start;padding-left: 12px;padding-right: 12px;" id="room_user_1_type"></span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--    </div>--}}
{{--    <div>--}}
{{--        <h4 id="room_title"></h4>--}}
{{--        <h4 id="room_id" style="display: none;"></h4>--}}
{{--    </div>--}}
{{--    <div>--}}
{{--        <h4 id="room_user_2"  style="display:none;"></h4>--}}
{{--        <div class="d-flex">--}}
{{--                <img src="{{ asset('uploads/users/default.png')}}"--}}
{{--                style="height: 50px">--}}
{{--                <div style="display:grid">--}}
{{--                <span style="padding:12px;padding-bottom: 0px;" id="room_user_22"></span>--}}
{{--                <span class="text-muted" style="text-align-last: start;padding-left: 12px;padding-right: 12px;" id="room_user_2_type"></span>--}}
{{--            </div>--}}
{{--            </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="h-100 bg-white mb-1 px-5 py-1" id="chat" style="overflow-y:scroll;max-height: 65vh;min-height:65vh;">--}}
{{--    <div id="msgs"></div>--}}
{{--</div>--}}
<form action="" class="d-flex">
    <input type="text" name="" id="chat-text" data-url="{{route('admin.chat.store')}}"
        class="form-control">
    <button class="btn btn-primary" id="send_msg">Send </button>
</form>

</div>
