<?php

namespace App\Notifications;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
  
class LocalNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $data;
    public function __construct($data)
    {
        $this->data = $data;
        // Defer queueing until after DB commit without redeclaring trait property
        $this->afterCommit = true;
    }
    public function via($notifiable)
    {
        return ['database'];
    }
    public function toArray($notifiable)
    {
        // Normalize bilingual payloads if arrays are provided
        $titleRaw = $this->data['title'] ?? '';
        $bodyRaw  = $this->data['body']  ?? '';

        $locale = app()->getLocale() ?: 'en';
        $title_en = is_array($titleRaw) ? ($titleRaw['en'] ?? reset($titleRaw) ?: '') : (string)$titleRaw;
        $title_ar = is_array($titleRaw) ? ($titleRaw['ar'] ?? '') : '';
        $body_en  = is_array($bodyRaw)  ? ($bodyRaw['en']  ?? reset($bodyRaw)  ?: '') : (string)$bodyRaw;
        $body_ar  = is_array($bodyRaw)  ? ($bodyRaw['ar']  ?? '') : '';

        $title = is_array($titleRaw) ? ($locale === 'ar' ? ($title_ar ?: $title_en) : $title_en) : $title_en;
        $body  = is_array($bodyRaw)  ? ($locale === 'ar' ? ($body_ar  ?: $body_en)  : $body_en)  : $body_en;

        return [
            'title' => $title,
            'title_en' => $title_en,
            'title_ar' => $title_ar,
            'body' => $body,
            'body_en' => $body_en,
            'body_ar' => $body_ar,
            'target' => $this->data['target'] ?? '',
            'link'=>    $this->data['link']??'',
            'object'=>  $this->data['object']??'',
            'target_id' => $this->data['target_id']??'',
            'user' => $this->data['sender']??'',
        ];
    }
}
