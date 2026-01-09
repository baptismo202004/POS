<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Provide `->name` to match views that expect a `name` attribute
    public function getNameAttribute()
    {
        return $this->brand_name ?? $this->attributes['brand_name'] ?? null;
    }
}
