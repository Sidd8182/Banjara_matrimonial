<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'height_cm',
        'weight_kg',
        'marital_status',
        'mother_tongue',
        'religion',
        'caste',
        'sub_caste',
        'gotra',
        'profile_created_by',
        'country',
        'state',
        'city',
        'area_locality',
        'pincode',
        'current_address',
        'permanent_address',
        'willing_to_relocate',
        'profile_picture_path',
        'video_intro_path',
        'media_privacy',
        'contact_mobile',
        'contact_email',
        'whatsapp_number',
        'contact_visibility',
        'mobile_verified',
        'email_verified',
        'last_completed_step',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'willing_to_relocate' => 'boolean',
        'mobile_verified' => 'boolean',
        'email_verified' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (self $profile) {
            if (empty($profile->profile_id)) {
                $profile->profile_id = 'BM' . now()->format('ymd') . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function familyDetail()
    {
        return $this->hasOne(FamilyDetail::class);
    }

    public function educationDetail()
    {
        return $this->hasOne(EducationDetail::class);
    }

    public function careerDetail()
    {
        return $this->hasOne(CareerDetail::class);
    }

    public function lifestyleDetail()
    {
        return $this->hasOne(LifestyleDetail::class);
    }

    public function horoscopeDetail()
    {
        return $this->hasOne(HoroscopeDetail::class);
    }

    public function partnerPreference()
    {
        return $this->hasOne(PartnerPreference::class);
    }

    public function mediaGallery()
    {
        return $this->hasMany(MediaGallery::class)->orderBy('sort_order')->orderBy('id');
    }

    public function verification()
    {
        return $this->hasOne(Verification::class, 'profile_id');
    }
}
