<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class Evaluate extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'comment', 'comment_replay', 'rate', 'user2_id', 'order_id', 'user_id', 'nodes', 'active'
    ];

        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
    public function scopeInActive($query)
    {
        return $query->where('active', 0);
    }
    public function getActive()
    {
        return $this->active == 1 ? Lang::get('site.active') : Lang::get('site.inactive');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
      public function userData()
    {
        return $this->belongsTo(UserData::class, 'user_id');
    }
       public function userData2()
    {
        return $this->belongsTo(UserData::class, 'user2_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }
    public function rateAvg(){
        return $this->avg('rate');
    }
}
