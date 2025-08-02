<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class 	StatusWithCode extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'json',  'user_id','code'
    ];

        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
  
}
