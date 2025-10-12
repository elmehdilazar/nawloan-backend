<?php

namespace App\Notifications;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
  
class LocalNotification extends Notification
{
    use Queueable;
    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function via($notifiable)
    {
        return ['database'];
    }
    public function toArray($notifiable)
    {
         
       
        return [
            'title' => $this->data['title'],
            'body' => $this->data['body'],
            'target' => $this->data['target'],
            'link'=>    $this->data['link'],
            'object'=>    $this->data['object'],
            'target_id' => $this->data['target_id'],
            'user' => $this->data['sender'],
        ];
    }
}
