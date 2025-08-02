<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'order_id', 'pay_transaction_id', 'notes', 'user_id', 'change_by', 'payment_method_id', 'price'
    ];
        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function payTransaction()
    {
        return $this->belongsTo(PayTransaction::class, 'pay_transaction_id');
    }
    public function payMethod(){
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
