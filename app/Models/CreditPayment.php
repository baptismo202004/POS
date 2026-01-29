<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    protected $fillable = [
        'credit_id',
        'cashier_id',
        'payment_amount',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
    ];

    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
