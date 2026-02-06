<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'max_credit_limit',
        'phone',
        'address',
        'status',
        'created_by',
    ];
}
