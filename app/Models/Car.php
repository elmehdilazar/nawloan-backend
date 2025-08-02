<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class Car extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'name_en', 'name_ar', 'weight', 'active','image','frames'
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
}
