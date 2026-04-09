<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_cycle',
        'description',
        'is_active',
        'is_visible',
        'is_recommended',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
        'is_recommended' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function features()
    {
        return $this->hasMany(SubscriptionPlanFeature::class)->orderBy('sort_order')->orderBy('id');
    }
}
