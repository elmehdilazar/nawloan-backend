<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UListUser extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'user_id', 'u_list_id',
    ];
        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    /**
     * Get the user that owns the UListUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Get the user that owns the UListUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uList()
    {
        return $this->belongsTo(UList::class, 'u_list_id');
    }
}
