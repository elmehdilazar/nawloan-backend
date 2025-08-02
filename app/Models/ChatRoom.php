<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'type','order_id', 'description', 'active', 'visible',];

        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    /**
     * The rooms that belongs to the user
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_users')
            ->withTimestamps();
    }

 public function usersData()
    {
        return $this->belongsToMany(UserData::class, 'room_users')
            ->withTimestamps();
    }

    /**
     * Define room relationship
     *
     * @return mixed
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id');
    }

    /**
     * Join a chat room
     *
     * @param \App\User $user
     */
    public function join($user)
    {
        return $this->users()->attach($user);
    }


    /**
     * Leave a chat room
     *
     * @param \App\User $user
     */
    public function leave($user)
    {
        return $this->users()->detach($user);
    }
    /**
     * Get the user that owns the ChatRoom
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

