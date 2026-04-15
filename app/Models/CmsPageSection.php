<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'cms_page_id',
        'section_name',
        'section_type',
        'title',
        'body',
        'target_pages',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'target_pages' => 'array',
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }
}
