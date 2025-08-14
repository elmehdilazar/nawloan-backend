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
        $serverKey = 'AAAA6Dm3XnU:APA91bHpRq8D38H3n0qq5UEf2UYS2l_aGTL6IoE8QUrS-V_kx1puWHPy_n3yq9xjf6uQ6l1il8raHnclkDE11JdevvaqZOYopRuTW9HRuLY6eOBGyZ4VUxassGxwdUbqIAiFvOAreWiF';

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
