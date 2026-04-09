<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $table = 'verification';

    protected $fillable = [
        'profile_id',
        'profile_verified_badge',
        'id_proof_type',
        'id_proof_path',
        'photo_verified',
        'mobile_verified',
        'email_verified',
    ];

    protected $casts = [
        'profile_verified_badge' => 'boolean',
        'photo_verified' => 'boolean',
        'mobile_verified' => 'boolean',
        'email_verified' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
