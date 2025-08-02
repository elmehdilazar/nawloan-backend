<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayTransaction extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = ['transaction_id', 'amount', 'fee', 'currency', 'payment_type', 'status', 'payment_method', 'name_in_card', 'order_id', 'deleted_at','receiver_name','receiver_account','payment_perpose'];
        protected $casts = [
            'deleted_at'=> 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function order()
    {
        return $this->HasOne(Order::class, 'order_id');
    }
    public function transactions()
    {
        return $this->HasMany(Transaction::class, 'pay_transaction_id');
    }
}
