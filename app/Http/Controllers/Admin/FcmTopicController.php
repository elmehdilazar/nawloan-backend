<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class FcmTopicController extends Controller
{
    public function subscribe(Request $r)
    {
        $data = $r->validate(['token' => 'required|string']);
        $serverKey = config('services.fcm.server_key') ?? env('FCM_SERVER_KEY');

        // subscribe token to /topics/admins (no DB store)
        $resp = Http::withHeaders([
            'Authorization' => 'key='.$serverKey,
            'Content-Type'  => 'application/json',
        ])->post('https://iid.googleapis.com/iid/v1:batchAdd', [
            'to' => '/topics/admins',
            'registration_tokens' => [$data['token']],
        ]);

        if (!$resp->ok()) {
            return response()->json([
                'ok' => false,
                'status' => $resp->status(),
                'body' => $resp->body(),
            ], 500);
        }

        return response()->json(['ok' => true]);
    }
}
