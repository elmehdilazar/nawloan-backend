<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDelivered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $roomId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ChatMessage $message, $roomId)
    {
        $this->message = $message;
        $this->roomId = $roomId;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    { //.$this->roomId
        return new Channel('chat');
    }
    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }
    public function broadcastAs()
    {
        return 'chat';
    }
}
