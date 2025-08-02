<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'vat','ton_price','price', 'sub_total', 'desc', 'notes', 'order_id','user_id','driver_id','drivers_ids', 'status'
    ];
        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function OfferOrder()
    {
        return $this->hasOne(Order::class, 'id');
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function statuses()
    {
        return $this->hasMany(OfferStatus::class, 'offer_id');
    }
}
