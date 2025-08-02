<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;

class Article_category extends Model
{
    use HasFactory ,softDeletes ;

    protected $fillable = [
        'category_ar', 'category_en','category_desc_en',
        'category_desc_ar', 'meta_title_en','meta_title_ar',
        'slug_en', 'slug_ar','meta_desc_en',
        'meta_desc_ar',
    ];

    public function article(){
        return $this->hasMany(Career::class);
    }
    public function getActive()
    {
        return $this->active == 1 ? Lang::get('site.active') : Lang::get('site.inactive');
    }
}
