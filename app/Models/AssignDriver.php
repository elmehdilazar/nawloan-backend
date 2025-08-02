<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class AssignDriver extends Model

{
        protected $table = 'assign_driver';

    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'driver_id', 'status', 'order_id', 'user_id'
    ];

        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
 

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
