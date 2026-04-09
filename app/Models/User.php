<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'role',
        'profile_for',
        'marital_status',
        'date_of_birth',
        'height_cm',
        'religion',
        'mother_tongue',
        'current_city',
        'current_state',
        'current_country',
        'diet',
        'smoke',
        'drink',
        'about_me',
        'education',
        'education_detail',
        'occupation',
        'income',
        'company_name',
        'family_type',
        'father_occupation',
        'mother_occupation',
        'brothers',
        'sisters',
        'family_values',
        'manglik',
        'rashi',
        'nakshatra',
        'time_of_birth',
        'place_of_birth',
        'gotra',
        'profile_completion_step',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
