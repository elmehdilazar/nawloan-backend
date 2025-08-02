<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusQR extends Model
{
    use HasFactory;
    protected $table='order_status_qr';
    protected $fillable = [
    'order_id', 'type', 'payload', 'signature', 'expires_at', 'used_at'
];

}
