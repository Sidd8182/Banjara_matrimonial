<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'occupation_type',
        'company_name',
        'job_title',
        'annual_income_range',
        'work_location',
        'work_type',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
