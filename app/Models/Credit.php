<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_name',
        'phone',
        'email',
        'address',
        'sale_id',
        'cashier_id',
        'credit_amount',
        'paid_amount',
        'remaining_balance',
        'status',
        'date',
        'notes',
    ];

    protected $casts = [
        'credit_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }
}
