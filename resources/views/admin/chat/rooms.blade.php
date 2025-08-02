
@foreach($rooms as $index=>$room)
<li id="room-{{$room->id}}">
    <a href="#" onclick="selectRoom({{$room}})" data-id="{{$room->id}}" class="flex-align-center">
        
<img src="{{ asset(optional(optional($room->users[0])->userData)->image ?: 'uploads/users/default.png') }}" class="avatar">
        <span class="flex-column">
            <span class="flex-space">
                <span class="name">{{$room->users[0]['name']
                }}</span>
                <small>{{$room->messages->count() > 0 ?
                $room->messages[$room->messages->count() - 1 ]->created_at->diffForHumans() :
                $room->created_at->diffForHumans()}}</small>
            </span>
            <p>{{$room->messages->count() > 0 ? $room->messages[$room->messages->count() - 1 ]->body : ''}}</p>
            <span class="d-none">{{$room->title}}</span>
            @php $room_id=$room->id @endphp
        </span>
    </a>
</li>
@endforeach