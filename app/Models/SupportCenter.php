<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportCenter extends Model
{
    use HasFactory;
    public $guarded = [];
   protected $fillable = [
        'name',
        'email',
        'phone',
        'phone_code',
        'phone_number',
        'title',
        'desc',
        'notes',
        'message',
        'user_id',
        'replay',
        'replay_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replayBy()
    {
        return $this->belongsTo(User::class, 'replay_by');
    }
}
