<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Branch;

class ProductSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'branch_id',
        'serial_number',
        'status',
        'warranty_expiry_date',
        'sale_item_id',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
