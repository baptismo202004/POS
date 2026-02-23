<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'branch_id',
        'purchase_id',
        'unit_type_id',
        'quantity',
        'initial_quantity',
        'price',
        'sold',
        'reason',
        'notes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }
}
