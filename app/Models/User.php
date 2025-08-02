<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Facades\Lang;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable;

    public $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','email','password','phone','user_type','type','active','online','fcm_token','phone_verified_at'];

        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'email_verified_at' => 'datetime:Y-m-d H:i:s',
            'phone_verified_at' => 'datetime:Y-m-d H:i:s',
        ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password','remember_token',];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
    public function getActiveType(){
        return $this->active == 1 ? 'active' : 'inactive';

    }



    public function getName($value)
    {
        return ucfirst($value);
    }
    public function userData()
    {
        return $this->hasOne(UserData::class, 'user_id');
    }
    public function offers()
    {
        return $this->hasMany(Offer::class, 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
    public function driving()
    {
        return $this->hasMany(Offer::class, 'driver_id');
    }
    public function evaluates()
    {
        return $this->hasMany(Evaluate::class, 'user_id');
    }
    // public function evaluates2()
    // {
    //     return $this->hasMany(Evaluate::class, 'user2_id');
    // }

    public function drivers()
    {
        return $this->hasMany(UserData::class,'company_id');
    }

    public function bank()
    {
        return $this->hasOne(BankInfo::class,'user_id');
    }

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'user_id');
    }
    public function rooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'room_users')
        ->withTimestamps();
    }
    public function ulists()
    {
        return $this->hasMany(UListUser::class, 'room_users.id');
    }

    /**
     * Add a new room
     *
     * @param \App\Room $room
     */
    public function addRoom($room)
    {
        return $this->rooms()->attach($room);
    }

    /**
     * Check if user has joined room
     *
     * @param mixed $roomId
     *
     * @return bool
     */
    public function hasJoined($roomId)
    {
        $room = $this->rooms->where('id', $roomId)->first();

        return $room ? true : false;
    }
    public function hasOutstandingBalance()
{
    return $this->userData->outstanding_balance > 0;
}

public function shouldFreezeAccount()
{
    $today = now();
    $dueDate = now()->startOfMonth()->addDays(10); // Payment due date: 10th of each month

    return $this->hasOutstandingBalance() && $today->greaterThan($dueDate);
}

}

