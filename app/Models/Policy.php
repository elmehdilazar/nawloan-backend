<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Policy extends Model
{
    use HasFactory,softDeletes;
    protected $fillable = [
        'title_en',
        'body_en',
        'title_ar',
        'body_ar',
        'user_id',
    ];
}
