<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileMatchAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_profile_id',
        'action',
        'rejection_reason',
    ];
}
