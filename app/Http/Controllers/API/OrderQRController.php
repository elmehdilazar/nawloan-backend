<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusQR;
use App\Services\QrService;
use Carbon\Carbon;

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

        OrderStatusQR::where('order_id', $order->id)
            ->where('type', $payload['type'])
            ->where('signature', $signature)
            ->update(['used_at' => now()]);

        return response()->json(['success' => true, 'status' => $order->status]);
    }
}

