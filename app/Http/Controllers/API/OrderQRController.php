<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Services\QrService;
use Illuminate\Http\Request;
use App\Models\OrderStatusQR;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Notifications\LocalNotification;
use App\Notifications\FcmPushNotification;
use Illuminate\Support\Facades\Notification;

class OrderQRController extends Controller
{
    public function generateQR(Request $request, $orderId)
    {
        $request->validate([
            'type' => 'required|in:pick_up,receive'
        ]);

        $expiresAt = Carbon::now()->addMinutes(10);
        $payload = QrService::generatePayload($orderId, $request->type, $expiresAt);
        $signature = QrService::signPayload($payload);

        OrderStatusQR::create([
            'order_id' => $orderId,
            'type' => $request->type,
            'payload' => json_encode($payload),
            'signature' => $signature,
            'expires_at' => $expiresAt
        ]);

        return response()->json([
            'qr_payload' => base64_encode(json_encode([
                'payload' => $payload,
                'signature' => $signature
            ]))
        ]);
    }

    public function verifyQR(Request $request)
    {
        $request->validate([
            'qr_payload' => 'required'
        ]);

        $decoded = json_decode(base64_decode($request->qr_payload), true);
        $payload = $decoded['payload'];
        $signature = $decoded['signature'];

        if (!QrService::verifySignature($payload, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        if (Carbon::parse($payload['expires_at'])->isPast()) {
            return response()->json(['error' => 'QR expired'], 403);
        }

        $order = Order::findOrFail($payload['order_id']);
        $order->status = $payload['type'] === 'pick_up' ? 'pick_up' : 'delivered';
        $order->save();
        $user = User::find($order->user_id);
        $driver = User::find($order->driver_id);
        
        $title = $message = '';
        switch ($order->status) {

            case 'pick_up':
                $message = Lang::get('site.not_pick_up_order_msg');
                $title = Lang::get('site.not_pick_up_order');
                break;
            case 'delivered':
                $message = Lang::get('site.not_delivered_order_msg');
                $title = Lang::get('site.not_delivered_order');
                break;
        }
        $message .= ' ' . $order->id . ' ' . Lang::get('site.by') . ' ' . Lang::get('site.user') . ' ' . $user->name;

        // Build dual-locale payloads
        $object = [
            'order_id' => $order->id,
            'user_id'  => $order->user_id,
            'offer_id' => $order->offer_id,
            'status'   => $order->status,
        ];
        $map = [
            'pick_up'   => ['not_pick_up_order',     'not_pick_up_order_msg'],
            'delivered' => ['not_delivered_order',   'not_delivered_order_msg'],
        ];
        $titleKey = $map[$order->status][0] ?? 'not_pend_order';
        $bodyKey  = $map[$order->status][1] ?? 'not_pend_order_msg';

        $dataAdmin = [
            'title'     => $titleKey,
            'body'      => $bodyKey,
            'target'    => 'order',
            'object'    => $object,
            'link'      => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender'    => $user->name,
        ];
        $dataFront = [
            'title' => [
                'ar' => Lang::get('site.' . $titleKey, [], 'ar'),
                'en' => Lang::get('site.' . $titleKey, [], 'en'),
            ],
            'body' => [
                'ar' => Lang::get('site.' . $bodyKey, [], 'ar'),
                'en' => Lang::get('site.' . $bodyKey, [], 'en'),
            ],
            'target'    => 'order',
            'object'    => $object,
            'link'      => route('admin.orders.index', ['number' => $order->id]),
            'target_id' => $order->id,
            'sender'    => $user->name,
        ];

        $users = User::where('type', 'admin')->orWhere('type', 'superadministrator')->get();
        $provider = $order->driver_id ? User::find($order->driver_id) : null;
        $user_sekker = User::find($order->user_id);
        if ($provider && !empty($provider->fcm_token)) {
            Notification::send($provider, new FcmPushNotification($title, $message, [$provider->fcm_token]));
            // Notification::send("fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg", new FcmPushNotification($title, $message, ["fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg"]));
        }
        if ($user_sekker && !empty($user_sekker->fcm_token)) {
            Notification::send($user_sekker, new FcmPushNotification($title, $message, [$user_sekker->fcm_token]));
            // Notification::send("fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg", new FcmPushNotification($title, $message, ["fMYK1Y4aImtQRe5Tqhru6A:APA91bGaUdFv2G_U5nuiHhjrWfrzpMrKgQ2sxPgh8NRy1-c56KWwrqaOm4GAQtFwgJuQ2-L4gVcO39b8TGIXhdxd96AMI4N4FkcFyOFkGix-sqw_KL4tzZg"]));
            Notification::send($user_sekker, new LocalNotification($dataFront));
        }
        foreach ($users as $user) {
            Notification::send($user, new FcmPushNotification($title, $message, [$user->fcm_token]));
            Notification::send($user, new LocalNotification($dataAdmin));
        }
        OrderStatusQR::where('order_id', $order->id)
            ->where('type', $payload['type'])
            ->where('signature', $signature)
            ->update(['used_at' => now()]);

        return response()->json(['success' => true, 'status' => $order->status]);
    }
}
