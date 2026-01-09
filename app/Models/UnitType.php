<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;

    protected $fillable = ['unit_name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Provide `->name` to match views that expect a `name` attribute
    public function getNameAttribute()
    {
        return $this->unit_name ?? $this->attributes['unit_name'] ?? null;
    }
}
