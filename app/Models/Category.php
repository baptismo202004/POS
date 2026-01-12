<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Provide `->name` to match views that expect a `name` attribute
    public function getNameAttribute()
    {
        return $this->category_name ?? $this->attributes['category_name'] ?? null;
    }
}
