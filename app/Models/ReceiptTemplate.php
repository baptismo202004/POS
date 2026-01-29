<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'header_content',
        'body_content',
        'footer_content',
        'css_styles',
        'settings',
        'is_default',
        'is_active',
        'paper_size',
        'orientation'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Scope to get only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get default template for a type
     */
    public function scopeDefaultForType($query, $type)
    {
        return $query->where('type', $type)->where('is_default', true);
    }

    /**
     * Get the full compiled template
     */
    public function getFullTemplateAttribute()
    {
        return ($this->header_content ?? '') . 
               ($this->body_content ?? '') . 
               ($this->footer_content ?? '');
    }

    /**
     * Set as default template for its type
     */
    public function setAsDefault()
    {
        // Remove default flag from other templates of same type
        static::where('type', $this->type)->where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }
}
