<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'age_min',
        'age_max',
        'height_min_cm',
        'height_max_cm',
        'religion_preference',
        'caste_preference',
        'location_preference',
        'preferred_cities',
        'minimum_qualification',
        'preferred_qualifications',
        'preferred_profession',
        'preferred_professions',
        'income_expectation',
        'diet_preference',
        'smoking_preference',
        'drinking_preference',
        'manglik_preference',
        'relocate_preference',
        'expectations',
    ];

    protected $casts = [
        'relocate_preference' => 'boolean',
        'preferred_cities' => 'array',
        'preferred_qualifications' => 'array',
        'preferred_professions' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
