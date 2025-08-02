<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FCMService
{
    public static function send($token, $notification)
    {
        
            
        $response = Http::withHeaders([
            'Authorization: key=' . 'AAAA6Dm3XnU:APA91bHpRq8D38H3n0qq5UEf2UYS2l_aGTL6IoE8QUrS-V_kx1puWHPy_n3yq9xjf6uQ6l1il8raHnclkDE11JdevvaqZOYopRuTW9HRuLY6eOBGyZ4VUxassGxwdUbqIAiFvOAreWiF',
            'Content-Type: application/json',
        ])->post("https://fcm.googleapis.com/fcm/send",   [
                'to' => $token,
                'notification' => $notification,
            ]);
        
        
   
        
      
    }
}
