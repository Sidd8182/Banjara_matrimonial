<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoroscopeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'date_of_birth',
        'time_of_birth',
        'place_of_birth',
        'birth_state',
        'rashi',
        'nakshatra',
        'lagna',
        'manglik',
        'horoscope_path',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
