<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'code',
        'rate',
        'type',
        'is_active',
        'description'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean'
    ];

    /**
     * Get formatted rate as percentage
     */
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate, 2) . '%';
    }

    /**
     * Scope to get only active taxes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
