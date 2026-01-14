<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type_id',
        'module',
        'ability',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
}
