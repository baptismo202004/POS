<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $fillable = ['name', 'description', 'parent_id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(UserType::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(UserType::class, 'parent_id');
    }
}
