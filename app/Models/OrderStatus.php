<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'order_id', 'status', 'notes','user_id', 'change_by','distance'
    ];
        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
