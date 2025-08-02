<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersInvites extends Model
{
    use HasFactory;
    public $guarded = [];

    protected $fillable = [
        'user_id', 'driver_id', 'order_id'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
     public function order()
    {
        return $this->belongsTo(Order::class, 'order_id'); // ✅ Fix: Correct reference to order_id
    }

    // Relationship with User (Who Sent the Invite)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // ✅ Fix: User who created the invite
    }

    // Relationship with User (Driver)
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id'); // ✅ Fix: User who is invited (driver)
    }

    // Relationship with Offers (Assuming an order has multiple offers)
    public function offers()
    {
        return $this->hasMany(Offer::class, 'order_id', 'order_id'); // ✅ Fix: Offers linked to the same order
    }
    
}
