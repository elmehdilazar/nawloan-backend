<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class Career extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'name_ar', 'name_en', 'active', 'category_id' , 'address_ar' ,'address_en',
        'desc_en','desc_ar','user_id'
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
    public function scopegetActive()
    {
        return $this->active == 1 ? Lang::get('site.active') : Lang::get('site.inactive');
    }

    public function category(){
        return $this->belongsTo(Career_category::class);
    }
    
        public function user(){
        return $this->belongsTo(User::class);
    }
}
