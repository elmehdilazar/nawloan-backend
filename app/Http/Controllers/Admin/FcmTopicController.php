<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Contract\Messaging; 
class FcmTopicController extends Controller
{
   
    public function __construct(private Messaging $messaging) {}

    public function subscribe(Request $r)
    {
        $data = $r->validate(['token' => 'required|string']);
        // HTTP v1 under the hood, using your service account:
        $this->messaging->subscribeToTopic('admins', $data['token']); 
        return response()->json(['ok' => true]);
    }
}
