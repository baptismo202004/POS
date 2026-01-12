<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = ['type_name', 'is_electronic'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Provide `->name` to match views that expect a `name` attribute
    public function getNameAttribute()
    {
        return $this->type_name ?? $this->attributes['type_name'] ?? null;
    }
}
