<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'brothers_count',
        'sisters_count',
        'family_type',
        'family_status',
        'family_values',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
