<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KundliMatchResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'male_profile_id',
        'female_profile_id',
        'guna_score',
        'guna_total',
        'percentage',
        'koota_breakdown',
        'source',
        'computed_at',
    ];

    protected $casts = [
        'koota_breakdown' => 'array',
        'computed_at' => 'datetime',
    ];

    public function maleProfile()
    {
        return $this->belongsTo(Profile::class, 'male_profile_id');
    }

    public function femaleProfile()
    {
        return $this->belongsTo(Profile::class, 'female_profile_id');
    }
}
