<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;
use Google\Client;
use Illuminate\Support\Facades\Log;
use Kutia\Larafirebase\Facades\Larafirebase;

class FcmPushNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $title;
    private $message;
    private $fcmTokens;
    private $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $message, $fcmTokens, array $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->fcmTokens = $fcmTokens;
        $this->data = $data;
        // Defer queueing until after database commit
        $this->afterCommit();
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
        // Validate token
        $token = $this->fcmTokens[0] ?? null;
        if (empty($token)) {
            Log::warning('[FCM] Missing FCM token; skipping send');
            return;
        }

        // Resolve absolute path to service account JSON
        $serviceAccountPath = base_path('public/assets/nawkey.json');
        if (!file_exists($serviceAccountPath)) {
            Log::error('[FCM] Service account JSON not found', [
                'path' => $serviceAccountPath,
            ]);
            return;
        }

// Your Firebase project ID
$projectId = 'nawloan-eff12';

// Example message payload
// Choose English as default for visible title/body; include bilingual in data
$titleEn = is_array($this->title) ? ($this->title['en'] ?? reset($this->title)) : $this->title;
$bodyEn  = is_array($this->message) ? ($this->message['en'] ?? reset($this->message)) : $this->message;
$titleAr = is_array($this->title) ? ($this->title['ar'] ?? $titleEn) : $this->title;
$bodyAr  = is_array($this->message) ? ($this->message['ar'] ?? $bodyEn) : $this->message;

$payload = [
  'token' => $token,
  'notification' => [
    'title' => $titleEn,
    'body'  => $bodyEn,
  ],
  'data' => array_merge([
    'title_en' => (string) $titleEn,
    'body_en'  => (string) $bodyEn,
    'title_ar' => (string) $titleAr,
    'body_ar'  => (string) $bodyAr,
  ], $this->data),
];
    try {
       Log::info('[FCM] Sending notification', [
           'project' => $projectId,
           'token_present' => !empty($this->fcmTokens[0]),
           'title' => $titleEn,
           'has_data' => !empty($this->data),
       ]);
       $accessToken = $this->getAccessToken($serviceAccountPath);
       $response = $this->sendMessage($accessToken, $projectId, $payload);
       Log::info('[FCM] Response', [
           'response' => $response,
       ]);

       // Clean up bad tokens to prevent repeated failures
       if (is_array($response) && isset($response['error'])) {
           $error = $response['error'];
           $details = $error['details'][0]['errorCode'] ?? null;
           if (in_array($details, ['UNREGISTERED', 'INVALID_ARGUMENT'], true)) {
               try {
                   User::where('fcm_token', $token)->update(['fcm_token' => null]);
                   Log::warning('[FCM] Cleared invalid token from user record', [
                       'errorCode' => $details,
                   ]);
               } catch (\Throwable $t) {
                   Log::error('[FCM] Failed clearing invalid token', [
                       'error' => $t->getMessage(),
                   ]);
               }
           }
       }
    } catch (\Exception $e) {
       Log::error('[FCM] v1 send failed; trying legacy key', [
           'error' => $e->getMessage(),
       ]);
   try {
       // Fallback to legacy API using larafirebase package
       Larafirebase::withTitle((string) $titleEn)
           ->withBody((string) $bodyEn)
           ->withAdditionalData(array_merge([
               'title_en' => (string) $titleEn,
               'body_en'  => (string) $bodyEn,
               'title_ar' => (string) $titleAr,
               'body_ar'  => (string) $bodyAr,
           ], $this->data))
           ->sendNotification($token);
       Log::info('[FCM] Legacy send dispatched');
   } catch (\Throwable $t) {
       Log::error('[FCM] Legacy send failed', [
           'error' => $t->getMessage(),
       ]);
   }
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
         throw new \Exception('Curl error: ' . curl_error($ch));
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
