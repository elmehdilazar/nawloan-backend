<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class UserData extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
        'commercial_record', 'commercial_record_image_f','commercial_record_image_b','tax_card','tax_card_image_f',
        'tax_card_image_b', 'national_id','national_id_image_f','national_id_image_b', 'track_type','driving_license_number','driving_license_image_f','driving_license_image_b',
        'track_license_number','track_license_image_f','track_license_image_b','track_image_f','track_image_b','track_image_s',
        'track_number','track_number_image_f', 'track_number_image_b','revision','company_id','desc','notes',
        'image','location','phone','type','user_id', 'status','vip', 'commission','balance','pending_balance','outstanding_balance', 'longitude', 'latitude','works_hours','date_of_payment'
    ];
        protected $casts = [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }
    public function company()
    {
        return $this->belongsTo(User::class,  'company_id');
    }
    public function car()
    {
        return $this->belongsTo(Car::class,  'track_type');
    }
 public function evaluates()
    {
        return $this->hasMany(Evaluate::class, 'user_id');
    }
    public function getRevision()
    {
        return $this->revision == 1 ? 'Revised' : 'Under Revision';
    }

 
}
