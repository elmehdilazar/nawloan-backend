<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Lang;

class Article extends Model
{
    use HasFactory ,softDeletes ;

    protected $fillable = [
        'article_ar', 'article_en','article_desc_en',
        'article_desc_ar', 'meta_title_en','meta_title_ar',
        'tage_en', 'tage_ar','meta_desc_en',
        'meta_desc_ar','article_date','image',"user_id",'category_id','article_date'
    ];


    public function images()
    {
        return $this->hasMany(Article_image::class, 'article_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Article_category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActive()
    {
        return $this->active == 1 ? Lang::get('site.active') : Lang::get('site.inactive');
    }

}
