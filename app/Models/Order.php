<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'user_id', 'car_id', 'pick_up_address','pick_up_late','pick_up_long','drop_of_address', 'drop_of_late','drop_of_long', 'shipment_type_id','shipment_details', 'spoil_quickly', 'breakable',
        'size', 'weight_ton','ton_price','total_price', 'shipping_date', 'status', 'service_provider','drivers_ids','desc','notes', 'payment_method_id','offer_id', 'code'
    ];
        protected $casts = [
            'shipping_date' => 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider');
    }
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
    public function shipmentType()
    {
        return $this->belongsTo(ShipmentType::class, 'shipment_type_id');
    }
    public function offers()
    {
        return $this->hasMany(Offer::class, 'order_id');
    }
    public function offer()
    {
        return $this->HasOne(Offer::class);
    }
    public function statuses()
    {
        return $this->hasMany(OrderStatus::class, 'order_id');
    } 
      public function invites()
    {
        return $this->hasMany(OrdersInvites::class, 'order_id');
    } 
    public function assignedDrivers()
    {
        return $this->hasMany(AssignDriver::class, 'order_id')->with('driver',function($xm){
                    $xm->with('userData')->get();
            
        });
    }
    public function evaluate()
    {
        return $this->hasOne(Evaluate::class, 'order_id');
    }
    public function transaction(){
        return $this->hasOne(PayTransaction::class,'order_id');
    }
    public function paymentType(){
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
    public function accountant()
    {
        return $this->hasOne(OrderAccountant::class, 'order_id', 'id');
    }
}

