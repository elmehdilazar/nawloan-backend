<?php
namespace App\Services;

class QrService
{
    public static function generatePayload($orderId, $type, $expiresAt)
    {
        return [
            'order_id' => $orderId,
            'type' => $type,
            'expires_at' => $expiresAt->toDateTimeString()
        ];
    }

    public static function signPayload(array $payload): string
    {
        $secret = config('app.key');
        return hash_hmac('sha256', json_encode($payload), $secret);
    }

    public static function verifySignature(array $payload, string $signature): bool
    {
        $expected = self::signPayload($payload);
        return hash_equals($expected, $signature);
    }
}
