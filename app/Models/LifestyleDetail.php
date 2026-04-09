<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LifestyleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'diet',
        'smoking',
        'drinking',
        'hobbies',
        'interests',
        'about_me',
    ];

    protected $casts = [
        'hobbies' => 'array',
        'interests' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
