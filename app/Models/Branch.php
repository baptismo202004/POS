<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['branch_name', 'address', 'assign_to', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }
}
