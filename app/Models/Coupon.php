<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Lang;

class Coupon extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'number_availabe',
        'discount',
        'apply_to',
        'start_date',
        'expiry_date',
        'user_id',
        'type',
        'active',
        'desc_en',
        'desc_ar',
    ];

    /**
     * Get the user that owns the Coupon
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getActive()
    {
        return $this->active == 1 ? Lang::get('site.active') : Lang::get('site.inactive');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', 0);
    }

}
