<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferStatus extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'offer_id', 'status', 'notes', 'user_id', 'change_by'
    ];
        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
