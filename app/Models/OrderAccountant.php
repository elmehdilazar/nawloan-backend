<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAccountant extends Model
{
    use HasFactory;

    public $guarded = [];
    protected $fillable = [
        'order_id', 'service_provider_amount', 'service_provider_commission', 'service_seeker_fee','vat', 'fine', 'operating_costs', 'diesel_cost',
        'expenses', 'active','status'
    ];
        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function order()
    {
        return $this->hasOne(Order::class, 'order_id');
    }
}
