<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'highest_qualification',
        'degree',
        'college_university',
        'field_of_study',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
