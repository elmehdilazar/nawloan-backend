<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;
use Google\Client;

class FcmPushNotification extends Notification
{
    use Queueable;
    private $title;
    private $message;
    private $fcmTokens;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $message, $fcmTokens)
    {
        $this->title = $title;
        $this->message = $message;
        $this->fcmTokens = $fcmTokens;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

    public function toFirebase($notifiable)
    {

// Path to your service account JSON key file
$serviceAccountPath = 'public/assets/nawkey.json';

// Your Firebase project ID
$projectId = 'nawloan-eff12';

// Example message payload
$message = [
 'token' => $this->fcmTokens[0],
 'notification' => [
 'title' => $this->title,
 'body' =>$this->message,
 ],
];
try {
   $accessToken = $this->getAccessToken($serviceAccountPath);
   $response = $this->sendMessage($accessToken, $projectId, $message);
//    echo 'Message sent successfully: ' . print_r($response, true);
} catch (Exception $e) {
//    echo 'Error: ' . $e->getMessage();
}

    }

    function getAccessToken($serviceAccountPath) {
        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
     }
     function sendMessage($accessToken, $projectId, $message) {
        $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        $headers = [
         'Authorization: Bearer ' . $accessToken,
         'Content-Type: application/json',
         ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
        $response = curl_exec($ch);
         if ($response === false) {
         throw new Exception('Curl error: ' . curl_error($ch));
         }
        curl_close($ch);
        return json_decode($response, true);
      }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
         foreach($this->fcmTokens as $token){

        $user=User::where('fcm_token', $token)->get()->first();
        return [
            'title' => $this->title,
            'body' => $this->message,
            'target' => $user->name,
            'link' =>    '#',
            'target_id' => $user->id,
            'user' => $user->name,
        ];
        }
    }
}
