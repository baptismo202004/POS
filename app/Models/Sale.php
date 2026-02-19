<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'cashier_id',
        'employee_id',
        'customer_id',
        'branch_id',
        'total_amount',
        'tax',
        'payment_method',
        'reference_number',
        'receipt_group_id',
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function receiptGroupSales()
    {
        return $this->hasMany(Sale::class, 'receipt_group_id', 'receipt_group_id')
                    ->where('receipt_group_id', '!=', null);
    }

    public function scopeFromSameReceipt($query, $receiptGroupId)
    {
        return $query->where('receipt_group_id', $receiptGroupId);
    }
}
