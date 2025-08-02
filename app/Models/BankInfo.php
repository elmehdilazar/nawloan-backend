<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class BankInfo extends Model
{
    use HasFactory;
    public $guarded = [];

    protected $fillable = [
        'user_id', 'bank_name', 'branch_name', 'account_holder_name','account_number','soft_code' ,'iban','active'
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
    public function user(){
        return $this->hasOne(User::class,'user_id');
    }
}
