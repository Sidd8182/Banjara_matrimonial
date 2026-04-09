<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaGallery extends Model
{
    use HasFactory;

    protected $table = 'media_gallery';

    protected $fillable = [
        'profile_id',
        'file_path',
        'media_type',
        'sort_order',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
